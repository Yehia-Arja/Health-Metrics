import React, { useState } from 'react';
import { Line } from 'react-chartjs-2';
import Button from "../../components/Button";

import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import './styles.css';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

const Dashboard = ({ dailyData, weeklyData }) => {
    const [view, setView] = useState('daily');
    
    const data =
        view === 'daily'
        ? {
            labels: dailyData.dates,
            datasets: [
                {
                label: 'Steps',
                data: dailyData.steps,
                fill: false,
                backgroundColor: '#3535F3',
                borderColor: '#3535F3',
                },
            ],
            }
        : {
            labels: weeklyData.weeks,
            datasets: [
                {
                label: 'Steps',
                data: weeklyData.steps,
                fill: false,
                backgroundColor: '#3535F3',
                borderColor: '#3535F3',
                },
            ],
            };

    const options = {
        responsive: true,
        plugins: {
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: view === 'daily' ? 'Daily Activity Trends' : 'Weekly Activity Trends',
        },
        },
    };

    return (
        <div className="dashboard-container">
        <h2 className="dashboard-heading">Dashboard</h2>
        <div className="dashboard-toggle-container">
            <Button
                className="toggle-button"
                text={view === 'daily' ? 'Daily View' : 'Weekly View'}
                onClick={() => {
                    view === 'daily' ? setView('weekly') : setView('daily');
                }}            
            />
        </div>
        <Line data={data} options={options} />
        </div>
  );
};

export default Dashboard;
