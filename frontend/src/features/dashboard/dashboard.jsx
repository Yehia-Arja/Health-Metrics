import React, { useState } from 'react';
import { Line } from 'react-chartjs-2';
import Button from "../../components/Button";
import useDashboardLogic from './useDashboardLogic';
import 'chartjs-adapter-date-fns';
import './styles.css';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    TimeScale
} from 'chart.js';


ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  TimeScale
);

const Dashboard = () => {
    const {
        view,
        setView,
        type,
        setType,
        filterYear,
        setFilterYear,
        filterPeriod,
        setFilterPeriod,
        activities,
        loading,
        filteredData,
        chartOptions,
        handleFileUpload,
    } = useDashboardLogic();

    const [yearInput, setYearInput] = useState(filterYear);
    const [periodInput, setPeriodInput] = useState(filterPeriod);

    if (loading) {
        return (
        <div className="loading-container">
            <h2 className="loading-text">Loading...</h2>
        </div>
        );
    }

    const labelYear = 'Year';
    const labelPeriod = view === 'daily' ? 'Week' : 'Month';

    const handleYearChange = (e) => {
        const val = e.target.value;
        if (/^\d*$/.test(val)) {
        setYearInput(val);
        setFilterYear(val);
        }
    };

    const handlePeriodChange = (e) => {
        const val = e.target.value;
        if (/^\d*$/.test(val)) {
        setPeriodInput(val);
        setFilterPeriod(val);
        }
    };

    const currentData = filteredData();

    return (
        <div className="dashboard-container">
        <h2 className="dashboard-heading">Dashboard</h2>
        <div className="dashboard-toggle-container">
            <Button
            className="toggle-button"
            text={view === 'daily' ? 'Daily' : 'Weekly'}
            onClick={() => {
                setView(view === 'daily' ? 'weekly' : 'daily');
            }}
            />
            <select
            value={type}
            onChange={(e) => setType(e.target.value)}
            className="activity-dropdown"
            >
            {activities.map((activity) => (
                <option key={activity} value={activity}>
                {activity.charAt(0).toUpperCase() + activity.slice(1).replace('_', ' ')}
                </option>
            ))}
            </select>
            <div className="number-input-container">
            <label>{labelYear}</label>
            <input
                type="text"
                value={yearInput}
                onChange={handleYearChange}
                className="year-input"
                placeholder="Enter year (e.g., 2020)"
            />
            </div>
            <div className="number-input-container">
            <label>{labelPeriod}</label>
            <input
                type="text"
                value={periodInput}
                onChange={handlePeriodChange}
                className="week-month-input"
                placeholder={`Enter ${labelPeriod.toLowerCase()} (e.g., 01)`}
            />
            </div>
            <input type='file' className='file-input' accept='.csv' onChange={(e) => handleFileUpload(e)} />
        </div>
        <Line key={`${view}-${type}-${filterYear}-${filterPeriod}`} data={currentData} options={chartOptions} />
        </div>
    );
};

export default Dashboard;
