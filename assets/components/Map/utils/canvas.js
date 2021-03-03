import { fix, getUniqImagesPos } from "./helpers";

const defaultImageSize = 1201;
const targetImageSize = 1081;

/**
 * Draws the image to the canvas and returns a promise.
 *
 * @param {String} image
 * @param {Object} options
 * @param {CanvasRenderingContext2D} ctx
 */
function putImageData(image, options, ctx) {
  const url = process.env.BASE_URL + "uncompressed/" + image;
  let promise = new Promise((resolve, reject) => {
    let img = new Image();
    img.src = url;

    const myOptions = Object.assign({}, options);
    img.onload = () => {
      ctx.drawImage(
        img,
        myOptions.x * img.width,
        myOptions.y * img.height,
        myOptions.sw * img.width,
        myOptions.sh * img.height,
        myOptions.dx * img.width || 0, //- 1
        myOptions.dh * img.width || 0, //- 1
        myOptions.sw * img.width, //myOptions.sw * img.width,
        myOptions.sh * img.height //myOptions.sh * img.height,);
      );

      resolve(img.src);
    };
    img.onerror = () => {
      img.src = "/uncompressed/default.jpg";

      ctx.drawImage(
        img,
        myOptions.x * img.width,
        myOptions.y * img.height,
        myOptions.sw * img.width,
        myOptions.sh * img.height,
        myOptions.dx * img.width || 0, //- 1
        myOptions.dh * img.width || 0, //- 1
        myOptions.sw * img.width, //myOptions.sw * img.width,
        myOptions.sh * img.height //myOptions.sh * img.height,);
      );
      resolve(img.src);
    };
  });

  return promise;
}

/**
 * Creates the final canvas and returns the base64 data url of it.
 *
 * @param {HTMLCanvasElement} canvasA
 * @param {Array} promises
 *
 * @returns {Object}
 */
async function loadImgFromCanvas(canvasA, promises) {
  const arr = await Promise.all(promises);
  const unique = getUniqImagesPos(arr);

  const canvasB = document.createElement("canvas");
  const ctx = canvasB.getContext("2d");
  canvasB.width = targetImageSize;
  canvasB.height = targetImageSize;

  ctx.drawImage(canvasA, 0, 0, targetImageSize, targetImageSize);

  const dataUrl = canvasB.toDataURL("image/jpeg");
  const content = dataUrl.split("base64,")[1];

  return {
    base64: content,
    unique,
  };
}

/**
 * Does the required calculations which determine where the coordinates
 * lie on the images.
 *
 * @param {Array} unique
 * @param {CanvasRenderingContext2D} ctx
 * @param {Object} corners
 *
 * @returns {Object}
 */
async function calculateDismensions(unique, ctx, corners) {
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

    ctx.canvas.width = (options[0].sw + options[1].sw) * defaultImageSize - 1;
    ctx.canvas.height = (options[0].sh + options[2].sh) * defaultImageSize;

    let promises = [];
    unique.images.forEach((image, index) => {
      let promise = putImageData(image, options[index], ctx);
      promises.push(promise);
    });

    const base64 = await loadImgFromCanvas(ctx.canvas, promises);

    return base64;
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

      ctx.canvas.width = (options[0].sw + options[1].sw) * defaultImageSize - 1;
      ctx.canvas.height = options[0].sh * defaultImageSize;

      let promises = [];
      unique.images.forEach((image, index) => {
        let promise = putImageData(image, options[index], ctx);
        promises.push(promise);
      });

      const base64 = await loadImgFromCanvas(ctx.canvas, promises);

      return base64;
    } else {
      //first square
      const x1 = topLeft.lng - floor(topLeft.lng);
      const y1 = ceil(topLeft.lat) - topLeft.lat;

      const sh1 = 1 - y1;
      const sw1 = 1 - (x1 + ceil(topRight.lng) - topRight.lng);

      //second square
      const x2 = botLeft.lng - floor(botLeft.lng);
      const y2 = 0;
      const sh2 = ceil(botLeft.lat) - botLeft.lat;
      const sw2 = 1 - (x2 + ceil(botRight.lng) - botRight.lng);

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

      ctx.canvas.width = options[0].sw * defaultImageSize;
      ctx.canvas.height = (options[0].sh + options[1].sh) * defaultImageSize;

      let promises = [];
      unique.images.forEach((image, index) => {
        let promise = putImageData(image, options[index], ctx);
        promises.push(promise);
      });

      const base64 = await loadImgFromCanvas(ctx.canvas, promises);

      return base64;
    }
  } else {
    const image = unique.images[0];

    const x = topLeft.lng - floor(topLeft.lng);
    const y = ceil(topLeft.lat) - topLeft.lat;

    const sw = 1 - (x + (ceil(topRight.lng) - topRight.lng));
    const sh = 1 - (y + (botLeft.lat - floor(botLeft.lat)));

    const options = { x: fix(x), y: fix(y), sw: fix(sw), sh: fix(sh) };

    ctx.canvas.width = options.sw * defaultImageSize;
    ctx.canvas.height = options.sh * defaultImageSize;

    let promises = [];
    let promise = putImageData(image, options, ctx);
    promises.push(promise);

    const base64 = await loadImgFromCanvas(ctx.canvas, promises);

    return base64;
  }
}

export default calculateDismensions;
