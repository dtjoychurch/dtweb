<?php

namespace Database\Seeders;

use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRecordType;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\HeroSlide;
use App\Models\Testimony;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Deliberately builds a small discipleship *chain*, not just one isolated
     * pair: 陳牧師 mentors two people (柏均、阿明), and one of his disciples
     * (柏均) is himself mentoring two others (小華、志明) — so the same user
     * shows up as both a disciple and a mentor, and both mentors here have
     * more than one disciple. This exercises the "多對關係" UI (multiple
     * relationship cards on /discipleship, mixed roles) instead of just the
     * single 1:1 happy path.
     */
    public function run(): void
    {
        // 安全可以重複執行：如果示範資料已經灌過一次（用這個固定信箱當標記），
        // 就直接跳過，不會重複建立或撞到 unique email 而噴錯。這樣即使
        // Pre-Deploy Command 裡長期留著 db:seed，也不會每次部署都出問題。
        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command?->info('示範資料已經存在，跳過 seeding。');

            return;
        }

        $admin = User::factory()->create([
            'name' => '管理員',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $chenPastor = User::factory()->create([
            'name' => '陳牧師',
            'email' => 'mentor@example.com',
        ]);

        $boJun = User::factory()->create([
            'name' => '柏均',
            'email' => 'disciple@example.com',
        ]);

        $aMing = User::factory()->create([
            'name' => '阿明',
            'email' => 'aming@example.com',
        ]);

        $xiaoHua = User::factory()->create([
            'name' => '小華',
            'email' => 'xiaohua@example.com',
        ]);

        $zhiMing = User::factory()->create([
            'name' => '志明',
            'email' => 'zhiming@example.com',
        ]);

        // 陳牧師 → 柏均：主要示範關係，手寫內容較完整。
        $mainRelationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $chenPastor->id,
            'disciple_id' => $boJun->id,
            'started_at' => now()->subMonths(4),
            'status' => 'active',
        ]);

        $sessions = collect(range(1, 5))->map(function (int $i) use ($mainRelationship, $chenPastor) {
            return $mainRelationship->sessions()->create([
                'created_by' => $chenPastor->id,
                'session_date' => now()->subWeeks(5 - $i),
                'title' => "第 {$i} 次門訓",
                'content' => "這是第 {$i} 次門訓的內容紀錄，我們談到了生活與信仰上的觀察與反思。",
            ]);
        });

        foreach ($sessions as $session) {
            $session->comments()->create([
                'user_id' => $boJun->id,
                'content' => '這次門訓對我很有幫助，謝謝陪伴。',
            ]);
        }

        $boJun->notes()->create([
            'session_id' => $sessions->last()->id,
            'title' => '門訓後的想法',
            'content' => '這是只有我自己看得到的筆記，記錄下這次門訓後的反思。',
        ]);

        DiscipleshipRecord::factory()->create([
            'relationship_id' => $mainRelationship->id,
            'created_by' => $boJun->id,
            'type_id' => DiscipleshipRecordType::where('slug', 'growth')->value('id'),
            'title' => '開始學習面對自己的情緒',
            'visibility' => 'shared',
            'occurred_at' => now()->subMonths(2),
        ]);

        DiscipleshipRecord::factory()->create([
            'relationship_id' => $mainRelationship->id,
            'created_by' => $boJun->id,
            'type_id' => DiscipleshipRecordType::where('slug', 'struggle')->value('id'),
            'title' => '面對工作與生活的掙扎',
            'visibility' => 'private',
            'occurred_at' => now()->subMonth(),
        ]);

        DiscipleshipGoal::factory()->create([
            'relationship_id' => $mainRelationship->id,
            'created_by' => $chenPastor->id,
            'title' => '建立穩定的靈修習慣',
            'status' => 'in_progress',
        ]);

        // 陳牧師 → 阿明：同一位 mentor 帶第二位 disciple。
        $this->quickRelationship($chenPastor, $aMing, monthsAgo: 2, sessionCount: 3);

        // 柏均 → 小華 / 柏均 → 志明：柏均同時是「被門訓者」也是「門訓者」，
        // 而且他自己也帶了不只一位 disciple。
        $this->quickRelationship($boJun, $xiaoHua, monthsAgo: 3, sessionCount: 4);
        $this->quickRelationship($boJun, $zhiMing, monthsAgo: 1, sessionCount: 2);

        // 首頁 Hero（可在後台「首頁 Hero」管理，這裡先放預設的三張）。
        HeroSlide::create([
            'image_path' => 'img/hero1.jpg',
            'title' => '一起走一段路，記下生命被改變的痕跡。',
            'subtitle' => '門訓不只是學習，而是一段彼此陪伴、一起成長的旅程。',
            'sort_order' => 1,
        ]);
        HeroSlide::create([
            'image_path' => 'img/hero2.jpg',
            'title' => '每一次的談話，都是生命被塑造的痕跡。',
            'subtitle' => '記錄下門訓路上的每一步同行。',
            'sort_order' => 2,
        ]);
        HeroSlide::create([
            'image_path' => 'img/hero3.jpg',
            'title' => '持續同行，關係不斷延續。',
            'subtitle' => '在真實的陪伴裡，看見生命的成長。',
            'sort_order' => 3,
        ]);

        // 見證分享（可在後台「見證分享」管理，這裡先放兩篇範例，內容故意寫長一點、
        // 分好幾個段落，讓詳細頁看起來像一篇真正的文章）。
        Testimony::create([
            'image_path' => 'img/introduce.jpg',
            'title' => '在門訓中重新找回信心',
            'content' => <<<'TEXT'
一年前的我，其實正處在一段很低潮的時間。工作上接連的挫折，加上人際關係的疏離，讓我開始懷疑自己是不是哪裡出了問題。那時候的我，習慣把所有情緒都藏起來，覺得只要表現得沒事，事情就真的會沒事。

轉捩點是一次很平常的聚會後，陳牧師約我喝咖啡，只是單純地問我「最近過得好嗎」。我原本想照例回答「還可以」，但那天不知道為什麼，眼淚突然就掉下來了。那是我第一次在別人面前，誠實地說出「我不好」。

從那次之後，我們開始了每週固定的門訓時間。一開始其實很不習慣，因為門訓不是上課，沒有標準答案，只是很單純地一起讀經、一起禱告、一起面對生活裡真實發生的事。有幾次我甚至覺得，把自己的軟弱攤開來給別人看，比想像中還要困難。

但也就是在這樣一次次誠實的對話裡，我開始慢慢學會接納自己的不完美。陳牧師從來沒有給過我「標準答案」，反而更多時候是安靜地陪我一起面對問題，讓我自己去經歷神在其中的帶領。

現在回頭看，這一年最大的改變不是環境變好了，而是我學會了在困難中仍然可以誠實、仍然可以尋求陪伴。感謝這段門訓關係，讓我重新找回面對生活的信心與盼望。
TEXT,
            'sort_order' => 1,
        ]);
        Testimony::create([
            'image_path' => 'img/method1.jpg',
            'title' => '學會陪伴，也學會被陪伴',
            'content' => <<<'TEXT'
成為 mentor 對我來說，一開始其實帶著不小的壓力。總覺得自己應該要有足夠的智慧、足夠的經驗，才能夠去帶領另一個人。第一次和柏均約時間門訓之前，我甚至緊張地把想講的內容寫成大綱，深怕自己講得不夠好。

但第一次見面之後，我很快就發現自己想錯了。門訓從來不是「我教你」的單向關係，而是兩個人一起坐下來，誠實地聊生活、聊信仰、聊那些平常不太有機會說出口的掙扎。柏均問我的一些問題，常常也是我自己需要重新思考的問題。

有一次，他分享了工作上遇到的困難，說著說著也問了我：「牧師你自己遇到類似的狀況，是怎麼撐過去的？」那個當下我才意識到，原來陪伴一個人成長的過程，同時也是在誠實面對自己過去還沒處理好的傷口。

漸漸地，我開始享受這段每週固定的時間，不再把它當成一項「任務」，而是一段真實的關係。我們會一起禱告、一起檢視這一週的生活，偶爾也只是單純地聊聊近況。門訓帶給我的，遠比我當初預期的還要多。

如果要用一句話總結這段經歷，我會說：成為 mentor 之後，我才真正學會了「陪伴」這件事——原來陪伴不是單方面的給予，而是在彼此同行的過程裡，一起被更新、一起成長。
TEXT,
            'sort_order' => 2,
        ]);
        Testimony::create([
            'image_path' => 'img/method2.jpg',
            'title' => '從逃避到誠實面對的一年',
            'content' => <<<'TEXT'
我一直是那種遇到問題就選擇逃避的人。工作壓力大就換工作，關係出狀況就疏遠對方，好像只要不去面對，事情就會自己消失。直到有一次跟小組裡的一位弟兄聊起這個模式，他才點出我其實已經逃避了很多年。

那次談話之後，我鼓起勇氣開始了一段門訓關係。老實說前幾次的聚會我都很想放棄，因為每次被問到「那你打算怎麼面對」的時候，我都會有一種想轉移話題的衝動。原來誠實面對自己，比我想像中還要不舒服。

但陪伴我的弟兄很有耐心，他沒有逼我馬上給出答案，只是持續地陪我坐在那個不舒服的狀態裡，偶爾分享他自己也曾經逃避過的經歷。漸漸地，我發現「被陪伴著面對問題」跟「自己一個人硬撐」，是完全不一樣的感受。

大概半年後，我第一次主動去處理了一段擱置很久的關係，那是我這輩子第一次沒有選擇逃避。雖然結果不一定盡如人意，但那種「我終於誠實面對了」的感覺，讓我覺得整個人輕鬆了很多。

現在的我還在學習，還是會有想逃避的時候，但至少我知道，我不是一個人在面對這些。這就是門訓對我最大的意義——不是有人幫我解決問題，而是有人願意陪我一起面對問題。
TEXT,
            'sort_order' => 3,
        ]);
        Testimony::create([
            'image_path' => 'img/method3.jpg',
            'title' => '從說教到聆聽，我學到的一課',
            'content' => <<<'TEXT'
帶第一個門訓對象的時候，我犯了一個很明顯的錯誤——我把每一次聚會都變成了小講道。對方分享一個困難，我就急著給建議、給經文、給解法，好像我的任務就是要把問題「解決掉」。

過了幾個月，對方才鼓起勇氣告訴我，其實他很多時候只是想找人說說話，不一定需要馬上得到答案。那句話讓我愣了很久，也讓我重新思考「陪伴」到底是什麼。

後來我開始練習閉上嘴巴，單純地聽。一開始很不習慣，甚至覺得自己好像什麼都沒有「貢獻」。但奇妙的是，當我不再急著給答案之後，對方反而分享得更深入、更誠實，我們的關係也變得更加真實。

我漸漸體會到，門訓不是知識的傳遞，而是生命與生命的靠近。很多時候，一個安靜的陪伴、一句「我聽到了」，比十段經文引用還要有力量。

這一課讓我重新調整了自己做 mentor 的方式，也讓我對「同行」這兩個字有了更深的體會——同行不是走在前面帶領，而是願意放慢腳步，陪對方一起走。
TEXT,
            'sort_order' => 4,
        ]);
        Testimony::create([
            'image_path' => 'img/people.jpg',
            'title' => '門訓陪我走過家庭關係的修復',
            'content' => <<<'TEXT'
過去幾年，我跟家人之間的關係一直很緊張，尤其是跟父親，我們常常一講話就吵起來，久而久之乾脆選擇不聯絡。這件事我從來沒有跟教會的人提起過，總覺得家醜不可外揚。

加入門訓之後，有一次聊到原生家庭，我才第一次把這件事說出口。沒想到陪伴我的姐妹並沒有評斷我，只是很溫柔地問我：「你希望這段關係變成什麼樣子？」這個問題我從來沒有認真想過。

接下來的幾個月，我們花了不少時間一起禱告、一起整理我對父親的情緒——那些委屈、那些沒有說出口的期待。她陪我練習寫一封信給父親，雖然到最後我沒有真的寄出去，但寫的過程本身就已經是一種釋放。

去年過年，我第一次主動打電話給父親，雖然對話還是有點生疏，但那已經是很久以來我們第一次好好說上幾句話。我知道修復關係不是一夜之間的事，但至少我踏出了第一步。

如果沒有這段門訓關係陪著我，我可能還停留在「不聯絡就好」的逃避狀態。感謝有人願意花時間陪我，一起面對這段連我自己都不太敢碰觸的關係。
TEXT,
            'sort_order' => 5,
        ]);
        Testimony::create([
            'title' => '一段還在進行中的故事',
            'content' => <<<'TEXT'
比起其他人的見證，我的故事還沒有一個漂亮的結局。我現在還在門訓的過程裡，還在學習怎麼誠實面對自己的軟弱，怎麼在關係中不逃避、不偽裝。

會想寫下這篇分享，是因為我發現自己一直在等一個「夠好」的時刻才願意開口見證——等問題解決了、等自己變得更成熟了，才覺得有資格分享。但後來想想，門訓本來就不是等到終點才有意義的事，而是走在路上的每一步都算數。

這段時間，我學會了每週固定花時間安靜下來、誠實記錄自己的狀態，也學會了在困難的時候開口求助，而不是自己硬撐。這些看似很小的改變，對我來說其實是很大的突破。

我還不知道這段門訓關係最後會帶我走到哪裡，但我很感謝自己願意踏出開始的那一步，也感謝一路上陪伴我的人。也許你也正處在故事還沒寫完的階段，那也沒關係——重要的不是故事現在有多完整，而是你願不願意開始走這一段路。
TEXT,
            'sort_order' => 6,
        ]);
    }

    /**
     * Creates a relationship with a handful of sessions (+ one comment and
     * one record each) so every seeded pair has enough history to browse,
     * without hand-writing every row like the main demo relationship above.
     */
    private function quickRelationship(User $mentor, User $disciple, int $monthsAgo, int $sessionCount): DiscipleshipRelationship
    {
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
            'started_at' => now()->subMonths($monthsAgo),
            'status' => 'active',
        ]);

        collect(range(1, $sessionCount))->each(function (int $i) use ($relationship, $mentor, $disciple, $sessionCount) {
            /** @var DiscipleshipSession $session */
            $session = $relationship->sessions()->create([
                'created_by' => $i % 2 === 0 ? $disciple->id : $mentor->id,
                'session_date' => now()->subWeeks(($sessionCount - $i) * 2),
                'title' => "第 {$i} 次門訓",
                'content' => fake('zh_TW')->paragraphs(2, true),
            ]);

            $session->comments()->create([
                'user_id' => $mentor->id === $session->created_by ? $disciple->id : $mentor->id,
                'content' => fake('zh_TW')->sentence(12),
            ]);
        });

        DiscipleshipRecord::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $disciple->id,
            'visibility' => fake()->randomElement(['shared', 'shared', 'private']),
            'occurred_at' => now()->subWeeks(2),
        ]);

        DiscipleshipGoal::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
            'status' => fake()->randomElement(['pending', 'in_progress']),
        ]);

        $disciple->notes()->create([
            'session_id' => $relationship->sessions()->latest('session_date')->first()?->id,
            'title' => '門訓後的一些想法',
            'content' => fake('zh_TW')->paragraph(),
        ]);

        return $relationship;
    }
}
