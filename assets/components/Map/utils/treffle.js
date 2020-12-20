const token = process.env.TREFLE_TOKEN;

const response = fetch(`https://trefle.io/api/v1/distributions?token=${token}`)
  .then((response) => response.json())
  .then((res) => console.log(res));
// {
//   first: "/api/v1/distributions?page=1";
//   last: "/api/v1/distributions?page=37";
//   next: "/api/v1/distributions?page=2";
//   self: "/api/v1/distributions";
// }
