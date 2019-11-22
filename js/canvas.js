var canvas = document.getElementById('canvassample'),
    ctx = canvas.getContext('2d'),
    moveflg = 0,
    Xpoint,
    Ypoint;

//初期値（サイズ、色、アルファ値）の決定
var defSize = 2,
    defColor = "#555";


// ストレージの初期化
var myStorage = localStorage;
window.onload = initLocalStorage();

// PC対応
canvas.addEventListener('mousedown', startPoint, false);
canvas.addEventListener('mousemove', movePoint, false);
canvas.addEventListener('mouseup', endPoint, false);
// スマホ対応
canvas.addEventListener('touchstart', startPoint, false);
canvas.addEventListener('touchmove', movePoint, false);
canvas.addEventListener('touchend', endPoint, false);

function startPoint(e){
  e.preventDefault();
  ctx.beginPath();

  Xpoint = e.layerX;
  Ypoint = e.layerY;

  ctx.moveTo(Xpoint, Ypoint);
}

function movePoint(e){
  if(e.buttons === 1 || e.witch === 1 || e.type == 'touchmove')
  {
    Xpoint = e.layerX;
    Ypoint = e.layerY;
    moveflg = 1;

    ctx.lineTo(Xpoint, Ypoint);
    ctx.lineCap = "round";
    ctx.lineWidth = defSize * 2;
    ctx.strokeStyle = defColor;
    ctx.stroke();

  }
}

function endPoint(e)
{

    if(moveflg === 0)
    {
       ctx.lineTo(Xpoint-1, Ypoint-1);
       ctx.lineCap = "round";
       ctx.lineWidth = defSize * 2;
       ctx.strokeStyle = defColor;
       ctx.stroke();

    }
    moveflg = 0;
  setLocalStoreage();
}

function clearCanvas(){
    if(confirm('Canvasを初期化しますか？'))
    {
        initLocalStorage();
        temp = [];
        resetCanvas();
    }
}

function resetCanvas() {
    ctx.clearRect(0, 0, ctx.canvas.clientWidth, ctx.canvas.clientHeight);
}

function chgImg()
{
  var png = canvas.toDataURL();

  document.getElementById("newImg").src = png;
}

function initLocalStorage(){
    myStorage.setItem("__log", JSON.stringify([]));
}
function setLocalStoreage(){
    var png = canvas.toDataURL();
    var logs = JSON.parse(myStorage.getItem("__log"));

    setTimeout(function(){
        logs.unshift({0:png});

        myStorage.setItem("__log", JSON.stringify(logs));

        currentCanvas = 0;
        temp = [];
    }, 0);
}

function prevCanvas(){
    var logs = JSON.parse(myStorage.getItem("__log"));

    if(logs.length > 0)
    {
        temp.unshift(logs.shift());

        setTimeout(function(){
            myStorage.setItem("__log", JSON.stringify(logs));
            resetCanvas();

            draw(logs[0]['png']);

        }, 0);
    }
}

function nextCanvas(){
    var logs = JSON.parse(myStorage.getItem("__log"));

    if(temp.length > 0)
    {
        logs.unshift(temp.shift());

        setTimeout(function(){
            myStorage.setItem("__log", JSON.stringify(logs));
            resetCanvas();

            draw(logs[0]['png']);

        }, 0);
    }
}

function draw(src) {
    var img = new Image();
    img.src = src;

    img.onload = function() {
        ctx.drawImage(img, 0, 0);
    }
}