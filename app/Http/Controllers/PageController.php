<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function about()
    {
        return view('front.pages.about');
    }

    /**
     * 門徒概要 15 點——原文逐字取自 Joy Church 門訓資料庫
     * (https://dtdatacenter.brian1024brian1024.workers.dev/disciple-profile)，
     * 每一點都個別到對應的原始頁面核對過文字，沒有自己加寫或改寫內容。
     * 作為門訓歷程中檢視生命成長的參考清單。屬於固定的靜態參考內容，
     * 不需要後台管理，因此直接寫在這裡。
     */
    public function resources()
    {
        $base = 'https://dtdatacenter.brian1024brian1024.workers.dev/disciple-profile/';

        $discipleProfilePoints = [
            [
                'title' => '耶穌為中心',
                'content' => '我刻意與神保持溝通、學習聆聽住在我裡面的聖靈的聲音、在日常生活中努力順服神，藉此活出以耶穌為中心的生命。我信靠神，我會根據神在我身上的旨意來作各種決定。',
                'url' => $base.'01-jesus-the-centre/',
            ],
            [
                'title' => '經常吸收神的話語',
                'content' => '我持續實踐一種生活習慣，就是經常吸收神的話語，在不同的生活層面中應用出來，並按照聖經真理來建立我的價值系統。我根據神話語的教導來生活，用神的話語與別人互動。',
                'url' => $base.'02-regular-intake/',
            ],
            [
                'title' => '與耶穌緊密同行',
                'content' => '我會分辨和勝過生命中的試探和捆綁。我與神緊密同行，不拖延處理與神關係的障礙。我開放生命，包括讓別人為我禱告，接受幫助，我也會向人交代。',
                'url' => $base.'03-close-walk-with-jesus/',
            ],
            [
                'title' => '做個忠心的人',
                'content' => '我學習成為忠心的人──忠誠、負責和可靠。在我個人生活、家庭生活和職場生活上都要顯為忠心。',
                'url' => $base.'04-faithful-person/',
            ],
            [
                'title' => '生活中犧牲',
                'content' => '我實踐在生活中學習犧牲，並經歷「施比受更為有福」的真諦。',
                'url' => $base.'05-living-sacrificially/',
            ],
            [
                'title' => '建立良好關係',
                'content' => '我努力建立良好、互信的人際關係，學習接納和愛我日常所接觸的人。',
                'url' => $base.'06-good-relationships/',
            ],
            [
                'title' => '寬恕別人',
                'content' => '當別人得罪我，我懂得寬恕；當我得罪別人，我會尋求他們寬恕。',
                'url' => $base.'07-forgiving-others/',
            ],
            [
                'title' => '積極的態度',
                'content' => '我根據神的話語發展積極生活的態度，在人生處境中保持喜樂、感恩和堅忍。',
                'url' => $base.'08-positive-attitude/',
            ],
            [
                'title' => '群體的一份子',
                'content' => '在信徒群體中我是忠心和活躍的一份子、會聽從領袖並與他一起活出神對我們群體的呼召。我一直預備自己參與開荒新群體。',
                'url' => $base.'09-part-of-a-community/',
            ],
            [
                'title' => '為耶穌作見證',
                'content' => '在日常生活中，我以言語、態度和行為積極地見證耶穌，讓人更願意認識祂。我主動領人歸主。',
                'url' => $base.'10-witnessing-for-jesus/',
            ],
            [
                'title' => '門訓別人',
                'content' => '我會門訓初信或慕道者，在他們生命中建立基督門徒的質素。',
                'url' => $base.'11-disciple-others/',
            ],
            [
                'title' => '憐憫的心腸',
                'content' => '我致力栽培自己憐憫的心腸，關懷周遭及海外有需要及被忽略（未得）的人。我會積極幫助他們並向他們傳福音。',
                'url' => $base.'12-compassionate-heart/',
            ],
            [
                'title' => '受教的心',
                'content' => '我有受教的心和渴望生命成長。我熱衷於從門訓者及神安置在我生命中的人身上學習。',
                'url' => $base.'13-teachable-heart/',
            ],
            [
                'title' => '人生召命',
                'content' => '我尋求愈加清晰了解神對我的人生召命，靠著聖靈和門訓者的幫助，向著神給我的召命逐步邁進。',
                'url' => $base.'14-life-mission/',
            ],
            [
                'title' => '強健的家庭',
                'content' => '我積極委身建立強健的家庭──在我的角色中學習盡責和做到最好。',
                'url' => $base.'15-strong-family-life/',
            ],
        ];

        return view('front.pages.resources', compact('discipleProfilePoints'));
    }
}
