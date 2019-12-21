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
 * Base64形式の文字列をBLOBに変換する
 */
function toBlob(base64) {
  var bin = atob(base64.replace(/^.*,/, ''));
  var buffer = new Uint8Array(bin.length);
  for (var i = 0; i < bin.length; i++) {
    buffer[i] = bin.charCodeAt(i);
  }
  // Blobを作成
  try {
    var blob = new Blob([buffer.buffer], {
      type: 'image/png'
    });
  } catch (e) {
    return false;
  }
  return blob;
}

/**
 * Loading イメージを表示する
 * @param {String} msg 画面に表示する文言
 */
function dispLoading(msg) {
  // 引数なし（メッセージなし）を許容
  if (msg == undefined) {
    msg = "処理中です．．．";
  }
  // 画面表示メッセージ
  var dispMsg = "<div class='loadingMsg'>" + msg + "</div>";
  // ローディング画像が表示されていない場合のみ出力
  if ($("#loading").length == 0) {
    $("body").append("<div id='loading'>" + dispMsg + "</div>");
  }
}


/**
 * Loading イメージを削除する
 */
function removeLoading() {
  $("#loading").remove();
}

/**
 * 全画面共通読み込み時処理
 */
$(function () {

  addEvents();
  ShowLayout();

  removeLoading();
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
      DeleteAllSign();
      ShowLayout();
    }
  });

  // 電子サインをアップロードする
  $('#btnUpload').on('click', function () {
    if (confirm('以下の伝票の電子サインをサーバーに登録します。よろしいですか？')) {
      dispLoading('処理中です');
      UploadSign();
      DeleteAllSign();
      return true;
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

  // サインを削除する
  $('.js-signdel').on('click', function () {
    if (confirm('この伝票の電子サインを削除します。よろしいですか？')) {
      let key = $(this).data('key');
      localStorage.removeItem(key);
      $(this).addClass('d-none');
    }
  });

  // サインを保存する
  $('#btnSaveImage').on('click', function () {
    let key = $(this).data('key');
    let png = document.getElementById('canvassample').toDataURL('image/png');

    SaveImage(key, png);
    $(this).data('key', '');
    $('.dlg_container').addClass('d-none');
    // 「サインを削除」ボタンを表示
    $('.js-signdel' + '[data-key="' + key + '"]').removeClass('d-none');
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

function DeleteAllSign() {
  let keys = new Array();
  for (i = 0; i < localStorage.length; i++) {
    keys.push(localStorage.key(i));
  }
  keys.forEach(function (key) {
    if (key.match(/^sign_.*$/)) {
      localStorage.removeItem(key);
    }
  });
}


function UploadSign() {
  //ローカルストレージのキーと画像バイナリデータを隠し項目に追加してフォームをサブミットする
  let keys = new Array();
  for (i = 0; i < localStorage.length; i++) {
    keys.push(localStorage.key(i));
  }
  keys.forEach(function (key) {
    if (key.match(/^sign_.*$/)) {
      let imgbase = localStorage.getItem(key);
      // let blob = toBlob(imgbase);
      let hidden = $('<input>', {
        type: 'hidden',
        name: key,
        value: imgbase,
      });
      $('#frm').append($(hidden));
    }
  });
}

function ShowLayout() {

  let keys = new Array();
  for (i = 0; i < localStorage.length; i++) {
    keys.push(localStorage.key(i));
  }
  keys.forEach(function (key) {
    if (key.match(/^sign_.*$/)) {
      $('.js-signdel' + '[data-key="' + key + '"]').removeClass('d-none');
    }
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
