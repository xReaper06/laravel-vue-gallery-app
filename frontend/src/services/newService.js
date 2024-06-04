import Swal from "sweetalert2"
import Api from "./Api";
import { localAPI } from "./myAPI";
export const getImage = (data) => `${localAPI()}/image/${data}`

export const successRes = (data) => {
    Swal.fire({
        title: "Success",
        text: data,
        icon: "success",
    });
};
export const errorRes = (data) => {
    Swal.fire({
        title: "Error",
        text: data,
        icon: "error",
    });
};

export const checkIfTheresUserLogin = async () => {
    const saveUser = localStorage.getItem("user");
    if (!saveUser) {
        // If there is no user saved, clear tokens and return true to redirect
        localStorage.removeItem("accessToken");
        return true;
    }

    const parsedUser = JSON.parse(saveUser);
    let formdata = {
        email: parsedUser.email,
    };

    try {
        const response = await Api().post("/checkUserifExist", formdata);
        console.log("API response:", response);

        if (!response.data.user || Object.keys(response.data.user).length < 1) {
            localStorage.removeItem("user");
            localStorage.removeItem("accessToken");
            return true;
        }

        return false;
    } catch (error) {
        console.error("Error checking user existence:", error);
        localStorage.removeItem("user");
        localStorage.removeItem("accessToken");
        return true;
    }
};