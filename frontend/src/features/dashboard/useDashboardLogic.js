import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchDashboardData, fetchActivities, uploadDashboardCsv } from './dashboardSlice';
import 'chartjs-adapter-date-fns';

const useDashboardLogic = () => {
    const dispatch = useDispatch();
    const { data, activities, loading } = useSelector((state) => state.dashboard);

    const [view, setView] = useState('daily');
    const [type, setType] = useState('steps');
    const [filterYear, setFilterYear] = useState('2020');
    const [filterPeriod, setFilterPeriod] = useState('01');

    const handleFileUpload = (event) => {
        dispatch(uploadDashboardCsv(event.target.files[0]));
    };

    useEffect(() => {
        dispatch(fetchDashboardData());
        dispatch(fetchActivities());
    }, [dispatch]);

    
    const filteredData = () => {
        if (!data || !data.daily_by_week || !data.weekly_by_month) {
            return { labels: [], datasets: [] };
        }

        if (view === 'daily') {
            const weekKey = `${filterYear}-W${filterPeriod.padStart(2, '0')}`;
            const weekData = data.daily_by_week[weekKey] || [];
            const labels = weekData.map(item => new Date(item.date));
            const values = weekData.map(item => {
            return item[type] || 0;
            
        });
        return {
            labels,
            datasets: [
            {
                label: type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' '),
                data: labels.map((label, i) => ({ x: label, y: values[i] })),
                fill: false,
                backgroundColor: '#3535F3',
                borderColor: '#3535F3',
            },
            ],
        };
        } else {
        
            const monthKey = `${filterYear}-${filterPeriod.padStart(2, '0')}`;
            const monthData = data.weekly_by_month[monthKey] || [];
            const labels = monthData.map(item => new Date(item.start_date));
            const values = monthData.map(item => {
            return item.total[type] || 0;
        });
        return {
            labels,
            datasets: [
            {
                label: type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' '),
                data: labels.map((label, i) => ({ x: label, y: values[i] })),
                fill: false,
                backgroundColor: '#3535F3',
                borderColor: '#3535F3',
            },
            ],
        };
        }
    };

    const chartOptions = {
        responsive: true,
        scales: {
        x: {
            type: 'time',
            time: {
            unit: view === 'daily' ? 'day' : 'week',
            tooltipFormat: 'MMM dd, yyyy',
            },
            ticks: {
            autoSkip: true,
            maxTicksLimit: 20,
            },
            title: {
            display: true,
            text: 'Date',
            },
        },
        y: {
            beginAtZero: true,
            title: {
            display: true,
            text: type.charAt(0).toUpperCase() + type.slice(1),
            },
        },
        },
        plugins: {
        legend: { position: 'top' },
        title: {
            display: true,
            text: view === 'daily' ? 'Daily Activity Trends' : 'Weekly Activity Trends',
        },
        },
    };

    return {
        view,
        setView,
        type,
        setType,
        filterYear,
        setFilterYear,
        filterPeriod,
        setFilterPeriod,
        data,
        activities,
        loading,
        filteredData,
        chartOptions,
        handleFileUpload,
    };
};

export default useDashboardLogic;
