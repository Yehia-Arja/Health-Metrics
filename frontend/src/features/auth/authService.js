import request from "../../utils/remote/axios";
import { requestMethods } from "../../utils/enum/requestMethods";

const authService = {
    login: async ({email, password}) => {
        const formData = new FormData();
        console.log("email", email);
        console.log("password", password);
        formData.append("email", email);
        formData.append("password", password);

        const response = await request({
        method: requestMethods.POST,
        route: "guest/login",
        body: formData,
        });
        console.log("response", response);
        return response;
    },
    
    register: async (email, password) => {
        const response = await request({
        method: requestMethods.POST,
        route: "auth/register",
        body: { email, password },
        });
        return response;
    },
    
    logout: async () => {
        const response = await request({
        method: requestMethods.POST,
        route: "auth/logout",
        });
        return response;
    },
}
export default authService;