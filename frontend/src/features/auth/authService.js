import request from "../../utils/remote/axios";
import { requestMethods } from "../../utils/enum/requestMethods";

const authService = {
    login: async ({ email, password }) => {
        const formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        const response = await request({
        method: requestMethods.POST,
        route: "guest/login",
        body: formData,
        });
        response.data ? localStorage.setItem('token', response.data.token) : null;
        return response;
    },
    
    signup: async ({ username, email, password }) => {
        const formData = new FormData();
        formData.append("username", username);
        formData.append("email", email);
        formData.append("password", password);

        const response = await request({
        method: requestMethods.POST,
        route: "guest/signup",
        body: formData,
        });
        return response;
    },
    
    logout: async () => {
        const response = await request({
        method: requestMethods.POST,
        route: "guest/logout",
        });
        return response;
    },

}
export default authService;