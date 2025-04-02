import request from '../../utils/remote/axios';
import {requestMethods} from '../../utils/enum/requestMethods';

const dashboardService = {
   
    getData: async () => {
        const response = await request({
            method: requestMethods.GET,
            route: 'guest/dashboard',
        })
        return response;
    },
    getActivities: async () => {
        const response = await request({
            method: requestMethods.GET,
            route: 'guest/activity',
        })
        return response;
    },
    uploadCsv: async (file) => {
        const formData = new FormData();
        formData.append('file', file);
        
        const response = await request({
            method: requestMethods.POST,
            route: 'guest/upload',
            data: formData,
        })
        return response;
    },
}

export default dashboardService;    