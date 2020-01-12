var canvas = document.getElementById('canvassample'),
  ctx = canvas.getContext('2d'),
  moveflg = 0,
  Xpoint,
  Ypoint;

//初期値（サイズ、色、アルファ値）の決定
var defSize = 2,
  defColor = "#000";

// PC対応
canvas.addEventListener('mousedown', startPoint, false);
canvas.addEventListener('mousemove', movePoint, false);
canvas.addEventListener('mouseup', endPoint, false);
// スマホ対応
canvas.addEventListener('touchstart', startPointM, false);
canvas.addEventListener('touchmove', movePointM, false);
canvas.addEventListener('touchend', endPointM, false);

function startPoint(e) {
  e.preventDefault();
  ctx.beginPath();

  Xpoint = e.layerX - window.pageXOffset;
  Ypoint = e.layerY - window.pageYOffset;

  ctx.moveTo(Xpoint, Ypoint);
}
function startPointM(e) {
  e.preventDefault();
  ctx.beginPath();
  var rect = e.target.getBoundingClientRect();
  undoImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
  for (var i = 0; i < finger.length; i++) {
    finger[i].x1 = e.touches[i].clientX - rect.left;
    finger[i].y1 = e.touches[i].clientY - rect.top;
  }
  ctx.moveTo(finger[i].x1, finger[i].y1);
}


function movePoint(e) {
  if (e.buttons === 1 || e.witch === 1 || e.type == 'touchmove') {
    Xpoint = e.layerX - window.pageXOffset;
    Ypoint = e.layerY - window.pageYOffset;
    moveflg = 1;
    ctx.lineTo(Xpoint, Ypoint);
    ctx.lineCap = "round";
    ctx.lineWidth = defSize * 2;
    ctx.strokeStyle = defColor;
    ctx.stroke();
  }
}
function movePointM(e) {
  if (e.buttons === 1 || e.witch === 1 || e.type == 'touchmove') {
    e.preventDefault();
    var rect = e.target.getBoundingClientRect();
    for (var i = 0; i < finger.length; i++) {
      finger[i].x = e.touches[i].clientX - rect.left;
      finger[i].y = e.touches[i].clientY - rect.top;
      moveflg = 1;
      ctx.lineTo(finger[i].x, finger[i].y);
      ctx.lineCap = "round";
      ctx.lineWidth = defSize * 2;
      ctx.strokeStyle = defColor;
      ctx.stroke();
      finger[i].x1 = finger[i].x;
      finger[i].y1 = finger[i].y;
    }
  }
}

function endPoint(e) {
  if (moveflg === 0) {
    ctx.lineTo(Xpoint - 1, Ypoint - 1);
    ctx.lineCap = "round";
    ctx.lineWidth = defSize * 2;
    ctx.strokeStyle = defColor;
    ctx.stroke();
  }
  moveflg = 0;
}

function endPointM(e) {
  if (moveflg === 0) {
    ctx.lineTo(finger[i].x - 1, finger[i].y - 1);
    ctx.lineCap = "round";
    ctx.lineWidth = defSize * 2;
    ctx.strokeStyle = defColor;
    ctx.stroke();
  }
  moveflg = 0;
}

var finger = new Array;
for (var i = 0; i < 10; i++) {
  finger[i] = {
    x: 0, y: 0, x1: 0, y1: 0,
    color: "rgb("
      + Math.floor(Math.random() * 16) * 15 + ","
      + Math.floor(Math.random() * 16) * 15 + ","
      + Math.floor(Math.random() * 16) * 15
      + ")"
  };
}




function clearCanvas() {
  if (confirm('Canvasを初期化しますか？')) {
    initLocalStorage();
    temp = [];
    resetCanvas();
  }
}

function resetCanvas() {
  ctx.clearRect(0, 0, ctx.canvas.clientWidth, ctx.canvas.clientHeight);
}

function chgImg() {
  var png = canvas.toDataURL();

  document.getElementById("newImg").src = png;
}


function draw(src) {
  var img = new Image();
  img.src = src;

  img.onload = function () {
    ctx.drawImage(img, 0, 0);
  }
}
