import apiInstance from "./api/apiInstance";

async function getAuth() {
  let responseCode;
  let admin;

  try {
    const request = await apiInstance.get("/user");
    responseCode = request.status;
    admin = request.data.user.roles.includes("ROLE_SUPER_ADMIN");
  } catch (error) {
    responseCode = error.response.status;
    admin = false;
  }

  return {
    auth: responseCode === 200,
    admin: admin,
  };
}

function removeAuth() {
  apiInstance.get("/logout");
  //check if status is 200 - unnecessary for now
}

export { getAuth, removeAuth };
