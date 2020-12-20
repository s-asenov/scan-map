import { fix } from "./helpers";

function putImageData(image, options, ctx) {
  const url = "https://localhost:8000/uncompressed/" + image;
  var promise = new Promise((resolve, reject) => {
    var img = new Image();
    img.src = url;
    img.onload = () => {
      const myOptions = Object.assign({}, options);
      ctx.drawImage(
        img,
        myOptions[`x`] * img.width,
        myOptions[`y`] * img.height,
        myOptions[`sw`] * img.width,
        myOptions[`sh`] * img.height,
        myOptions[`dx`] * img.width - 1 || 0,
        myOptions[`dh`] * img.width - 1 || 0,
        myOptions[`sw`] * img.width, //myOptions.sw * img.width,
        myOptions[`sh`] * img.height //myOptions.sh * img.height,);
      );
      resolve({
        x: myOptions[`x`] * img.width,
        y: myOptions[`y`] * img.height,
        sw: myOptions[`sw`] * img.width,
        sh: myOptions[`sh`] * img.height,
        dx: myOptions[`dx`] * img.width - 1 || 0,
        dy: myOptions[`dh`] * img.width - 1 || 0,
        sw: myOptions[`sw`] * img.width, //myOptions.sw * img.width,
        sh: myOptions[`sh`] * img.height,
      });
    };
    img.onerror = () => {
      this.src = "/uncompressed/default.jpg";
      resolve(img);
    };
  });
  return promise;
}

function loadImgFromCanvas(canvas, promises) {
  Promise.all(promises).then(() => {
    const dataUrl = canvas.toDataURL("image/jpeg");
    // let imageFoo = document.createElement("img");
    // imageFoo.src = dataUrl;
    // Style your image here
    // imageFoo.style.width = arr[0].sw + arr[1].sw;
    // imageFoo.style.height = arr[0].sh;
    let link = document.createElement("a");
    link.download = "filename";
    link.href = dataUrl;
    link.click();

    // After you are done styling it, append it to the BODY element
    // document.body.appendChild(imageFoo);
  });
}

function calculateDismensions(unique, ctx, corners) {
  const { topRight, topLeft, botRight, botLeft } = corners;
  const { floor, ceil } = Math;

  if (unique.count === 4) {
    //first
    const x1 = topLeft.lng - floor(topLeft.lng);
    const y1 = ceil(topLeft.lat) - topLeft.lat;
    const sw1 = 1 - x1;
    const sh1 = 1 - y1;
    //dx dh - 0 0

    //second
    const x2 = 0;
    const y2 = ceil(topRight.lat) - topRight.lat;
    const sw2 = topRight.lng - floor(topRight.lng);
    const sh2 = topRight.lat - floor(topRight.lat); // 1 - y2
    const dx2 = sw1;
    //dy 0

    //third
    const y3 = 0;
    const x3 = botLeft.lng - floor(botLeft.lng);
    const sw3 = ceil(botLeft.lng) - botLeft.lng; //1 - x3
    const sh3 = ceil(botLeft.lat) - botLeft.lat;
    // const dy3 = sh1;
    //dx 0

    //forth
    const x4 = 0;
    const y4 = 0;
    const sw4 = botRight.lng - floor(botRight.lng);
    const sh4 = ceil(botRight.lat) - botRight.lat;
    // const dx4 = sw1;
    // const dy4 = sh1;

    const options = [
      {
        x: fix(x1),
        y: fix(y1),
        sw: fix(sw1),
        sh: fix(sh1),
      },
      {
        x: fix(x2),
        y: fix(y2),
        sw: fix(sw2),
        sh: fix(sh2),
        dx: fix(sw1),
      },
      {
        x: fix(x3),
        y: fix(y3),
        sw: fix(sw3),
        sh: fix(sh3),
        dy: fix(sh1),
      },
      {
        x: fix(x4),
        y: fix(y4),
        sw: fix(sw4),
        sh: fix(sh4),
        dy: fix(sh1),
        dx: fix(sw1),
      },
    ];

    ctx.canvas.width = (options[0].sw + options[1].sw) * 1201 - 1;
    ctx.canvas.height = (options[0].sh + options[2].sh) * 1201;

    let promises = [];
    unique.images.forEach((image, index) => {
      let promise = putImageData(image, options[index], ctx);
      promises.push(promise);
    });

    loadImgFromCanvas(ctx.canvas, promises);
  } else if (unique.count === 2) {
    if (unique.direction === "horizontal") {
      //first square
      const x1 = topLeft.lng - floor(topLeft.lng);
      const y1 = ceil(topLeft.lat) - topLeft.lat;

      const sw1 = 1 - x1;
      const sh1 = 1 - (botLeft.lat - floor(botLeft.lat) + y1);

      //second square
      const x2 = 0;
      const y2 = ceil(topRight.lat) - topRight.lat;

      const sw2 = topRight.lng % floor(topRight.lng);
      const sh2 = 1 - (y2 + botRight.lat - floor(botRight.lat));

      //options
      const options = [
        {
          x: fix(x1),
          y: fix(y1),
          sw: fix(sw1),
          sh: fix(sh1),
        },
        {
          x: fix(x2),
          y: fix(y2),
          sw: fix(sw2),
          sh: fix(sh2),
          dx: fix(sw1),
        },
      ];

      ctx.canvas.width = (options[0].sw + options[1].sw) * 1201 - 1;
      ctx.canvas.height = options[0].sh * 1201;

      let promises = [];
      unique.images.forEach((image, index) => {
        let promise = putImageData(image, options[index], ctx);
        promises.push(promise);
      });

      loadImgFromCanvas(ctx.canvas, promises);
    } else {
      //first square
      const x1 = topLeft.lng - Math.floor(topLeft.lng);
      const y1 = Math.ceil(topLeft.lat) - topLeft.lat;

      const sh1 = 1 - y1;
      const sw1 = 1 - (x1 + Math.ceil(topRight.lng) - topRight.lng);

      //second square
      const x2 = botLeft.lng - Math.floor(botLeft.lng);
      const y2 = 0;
      const sh2 = Math.ceil(botLeft.lat) - botLeft.lat;
      const sw2 = 1 - (x2 + Math.ceil(botRight.lng) - botRight.lng);

      const options = [
        {
          x: fix(x1),
          y: fix(y1),
          sw: fix(sw1),
          sh: fix(sh1),
        },
        {
          x: fix(x2),
          y: fix(y2),
          sw: fix(sw2),
          sh: fix(sh2),
          dh: fix(sh1),
        },
      ];

      ctx.canvas.width = options[0].sw * 1201;
      ctx.canvas.height = (options[0].sh + options[1].sh) * 1201;

      let promises = [];
      unique.images.forEach((image, index) => {
        let promise = putImageData(image, options[index], ctx);
        promises.push(promise);
      });

      loadImgFromCanvas(ctx.canvas, promises);
    }
  } else {
    const image = unique.images[0];

    const x = topLeft.lng - Math.floor(topLeft.lng);
    const y = Math.ceil(topLeft.lat) - topLeft.lat;

    const sw = 1 - (x + (Math.ceil(topRight.lng) - topRight.lng));
    const sh = 1 - (y + (botLeft.lat - Math.floor(botLeft.lat)));

    const options = { x: fix(x), y: fix(y), sw: fix(sw), sh: fix(sh) };

    let promises = [];
    let promise = putImageData(image, options, ctx);
    promises.push(promise);

    loadImgFromCanvas(ctx.canvas, promises);
  }
}

export default calculateDismensions;
