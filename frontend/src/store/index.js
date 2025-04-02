import { configureStore } from '@reduxjs/toolkit';
import authReducer from '../features/auth/authSlice';
import dashboardReducer from '../features/dashboard/dashboardSlice';



const store = configureStore({
    reducer: {
        auth: authReducer,
        dashboard: dashboardReducer, 
    },
    middleware: (defaultMiddleware) =>
        defaultMiddleware({
            serializableCheck: false,
  }),

})

export default store;