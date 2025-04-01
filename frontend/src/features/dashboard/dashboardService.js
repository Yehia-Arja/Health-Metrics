import request from '../../utils/request';
import requestMethods from '../../utils/requestMethods';

const dashboardService = {
    getData: async () => {
        const response = await request({
            method: requestMethods.GET,
            url: '/dashboard',
        })
        return response;
    }
}
export default dashboardService;    