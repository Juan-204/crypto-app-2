import { useState, useEffect } from "react";
import { Line } from "react-chartjs-2";
import { Chart as ChartJS, LineElement, PointElement, LinearScale, Title, Tooltip, Legend, CategoryScale } from "chart.js";
import axios from "axios";

// Registrar los componentes de Chart.js
ChartJS.register(LineElement, PointElement, LinearScale, Title, Tooltip, Legend, CategoryScale);

const CryptoChart = ({ cryptocurrencyId }) => {
    const [historicalData, setHistoricalData] = useState([]);

    // Función para obtener los datos históricos
    const fetchHistoricalData = () => {
        axios.get(`/api/historical-data/${cryptocurrencyId}`)
            .then(response => {
                // Ordenar por fecha ascendente (más antiguo a la izquierda)
                const sortedData = response.data.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
                setHistoricalData(sortedData);
            })
            .catch(error => console.error("Error fetching historical data:", error));
    };


    // Efecto para cargar los datos iniciales y actualizar cada 1 segundos
    useEffect(() => {
        fetchHistoricalData(); // Carga inicial

        const interval = setInterval(() => {
            fetchHistoricalData(); // Se ejecuta cada
        }, 60000);

        return () => clearInterval(interval); // Limpiar intervalo al desmontar
    }, [cryptocurrencyId]);

    const data = {
        labels: historicalData.map(entry =>
            new Date(entry.timestamp).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: false })
        ),
        datasets: [
            {
                label: "Precio USD",
                data: historicalData.map(entry => entry.price),
                borderColor: "rgb(75, 192, 192)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderWidth: 2,
                pointRadius: 0, // Ocultar los puntos
                fill: true,
            }
        ]
    };

    return (
        <div className="w-full h-64">
            {historicalData.length > 0 ? (
                <Line data={data} options={{ responsive: true, maintainAspectRatio: false }} />
            ) : (
                <p className="text-gray-500">Cargando historial...</p>
            )}
        </div>
    );
};

export default CryptoChart;
