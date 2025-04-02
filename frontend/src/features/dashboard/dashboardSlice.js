import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import dashboardService from './dashboardService';

export const fetchDashboardData = createAsyncThunk(
	'dashboard/fetchDashboardData',
		async (_,{rejectWithValue }) => {
		try {
			const response = await dashboardService.getData();
		if (!response.success) {
			throw new Error('Failed to fetch dashboard data');
			}
			
		return response.data;
		} catch (error) {
		return rejectWithValue(error.message);
		}
	}
);
export const fetchActivities = createAsyncThunk(
	'dashboard/fetchActivities',
	async (_,{ rejectWithValue }) => {
		try {
			const response = await dashboardService.getActivities();
			if (!response.success) {
				throw new Error('Failed to fetch activities data');
			}
			return response.data;
		} catch (error) {
		return rejectWithValue(error.message);
		}
	}
);

export const uploadDashboardCsv = createAsyncThunk(
	'dashboard/uploadDashboardCsv',
	async (file, { rejectWithValue }) => {
		try {
			const response = await dashboardService.uploadCsv(file);
			if (!response.success) {
				throw new Error('Failed to upload CSV file');
			}
			return response.data;
		} catch (error) {
			return rejectWithValue(error.message);
		}
	}
)

const dashboardSlice = createSlice({
	name: 'dashboard',
	initialState: {
		data: {},
		activities: [],  
		loading: false,
		error: null,
	},
	reducers: {
		// Synchronous reducers can be added here
	},
	extraReducers: (builder) => {
		builder
		.addCase(fetchDashboardData.pending, (state) => {
			state.loading = true;
			state.error = null;
		})
		.addCase(fetchDashboardData.fulfilled, (state, action) => {
			state.loading = false;
			state.data = action.payload;
		})
		.addCase(fetchDashboardData.rejected, (state, action) => {
			state.loading = false;
			state.error = action.payload;
		});
		builder
			.addCase(fetchActivities.pending, (state) => {
				state.error = null;
			})
			.addCase(fetchActivities.fulfilled, (state, action) => {
				state.activities = action.payload;
			})
			.addCase(fetchActivities.rejected, (state, action) => {
				state.error = action.payload;
			});
		builder 
			.addCase(uploadDashboardCsv.pending, (state) => {
				state.loading = true;
				state.error = null;
			})
			.addCase(uploadDashboardCsv.fulfilled, (state) => {
				state.loading = false;
			})
			.addCase(uploadDashboardCsv.rejected, (state, action) => {
				state.loading = false;
				state.error = action.payload;
			});
	},
});

export default dashboardSlice.reducer;
