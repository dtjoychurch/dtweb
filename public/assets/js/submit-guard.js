// 防止表單被連續點擊或連續按 Enter 重複送出。
document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!(form instanceof HTMLFormElement)) return;

  // 如果表單本身的 onsubmit（例如 confirm() 對話框）已經取消了這次送出，
  // 就不要鎖住按鈕，否則使用者取消刪除後按鈕會卡在無法再點的狀態。
  if (e.defaultPrevented) return;

  if (form.dataset.submitGuard === '1') {
    e.preventDefault();
    return;
  }

  form.dataset.submitGuard = '1';
  form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
    btn.disabled = true;
  });
});
