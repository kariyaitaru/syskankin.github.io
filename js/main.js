window.onload = () => {
    'use strict';

    if ('serviceWorker' in navigator) {
      navigator.serviceWorker
        .register('./sw.js')
        .then(registration => {
          // 登録成功
          registration.onupdatefound = function() {
            console.log('アップデートがあります！');
            registration.update();
          }
        })
        .catch(err => {
          // 登録失敗
      });
    }
  }
