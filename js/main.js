window.onload = () => {
  'use strict';

  if ('serviceWorker' in navigator) {
    navigator.serviceWorker
      .register('./sw.js')
      .then(registration => {
        // 登録成功
        registration.onupdatefound = function () {
          console.log('アップデートがあります！');
          registration.update();
        }
      })
      .catch(err => {
        // 登録失敗
      });
  }
}

/**
 * 全画面共通読み込み時処理
 */
$(function () {

  addEvents();

});

/**
 * イベント定義
 */
function addEvents() {

  // キャッシュを削除する
  $('#btnClearCache').on('click', function () {
    navigator.serviceWorker.getRegistration()
      .then(registration => {
        registration.unregister();
      })
  });
  // ローカルストレージの画像データを削除する
  $('#btnDeleteStrage').on('click', function () {
    if (confirm('以下の伝票の電子サインを削除します。よろしいですか？')) {

      for (i = 0; i < localStorage.length; i++) {
        var key = localStorage.key(i)
        if (key.match(/^sign_.*$/)) {
          localStorage.removeItem(key)
        }
      }
    }
  });

  // サインを表示する
  $('.js-signshow').on('click', function () {
    ClearCanvas();
    $('.dlg_container').removeClass('d-none');

    let key = $(this).data('key');
    $('#btnSaveImage').data('key', key);

    LoadImage(key);
  });

  // サインを保存する
  $('#btnSaveImage').on('click', function () {
    // chgImg();
    let key = $(this).data('key');
    let png = document.getElementById('canvassample').toDataURL('image/png');

    SaveImage(key, png);
    $(this).data('key', '');
    $('.dlg_container').addClass('d-none');
  });

  // キャンバスをクリアする
  $('#btnDlgReset').on('click', function () {
    ClearCanvas();
  });

  // サイン画面を閉じる
  $('#btnDlgClose').on('click', function () {
    $('.dlg_container').addClass('d-none');
    ClearCanvas();
  });

}


function ClearCanvas() {
  let canvas = document.getElementById('canvassample');
  let ctx = canvas.getContext("2d");
  ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function SaveImage(key, img) {
  let setjson = JSON.stringify(img);
  localStorage.setItem(key, setjson);
}

function LoadImage(key) {
  let imgJson = localStorage.getItem(key);
  let img;
  if (imgJson) {
    img = new Image();
    img.src = JSON.parse(imgJson);

    //画像をcanvasに設定
    img.onload = function () {
      let canvas = document.getElementById('canvassample');
      let ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0, 300, 300);
    }
  }
}
