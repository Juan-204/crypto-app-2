import { useState, useEffect } from "react";
import SelectCrypto from "@/Components/SelectCrypto";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import axios from "axios";

export default function Dashboard({ auth }) {
    const [favorites, setFavorites] = useState([]);

    const formatFinancialNumber = (value, isCurrency = true, isPercent = false) => {
        if (value === "N/A" || value === null || value === undefined) return "N/A";

        const num = parseFloat(value);
        if (isNaN(num)) return "N/A";

        if (isPercent) {
        return `${num.toFixed(2)}%`;
        }

        const formatter = Intl.NumberFormat('en-US', {
        notation: "compact",
        maximumFractionDigits: 2,
        minimumFractionDigits: 2
        });

        return isCurrency ? `$${formatter.format(num)}` : formatter.format(num);
    };


    // Función para obtener la lista de favoritos con datos actualizados
    const fetchFavorites = () => {
        axios.get(`/api/favoritesIndex?user_id=${auth.user.id}`)
            .then(response => setFavorites(response.data))
            .catch(error => console.error("Error fetching favorites:", error));
    };

    // Llamar a la API cuando se carga la página
    useEffect(() => {
        fetchFavorites();

        const interval = setInterval(fetchFavorites, 60000);

        // Limpiar el intervalo cuando el componente se desmonta
        return () => clearInterval(interval);
    }, []);

    const handleAddFavorite = (newCrypto) => {
        setFavorites((prevFavorites) => [
            ...prevFavorites,
            {
                id: newCrypto.id,
                cryptocurrency: {
                    id: newCrypto.id,
                    name: newCrypto.name,
                    symbol: newCrypto.symbol,
                }
            }
        ]);
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <SelectCrypto auth={auth} onAddFavorite={handleAddFavorite} />

                    <h2 className="text-xl font-semibold mt-6">Favoritos</h2>
                    <ul className="mt-2">
                        {favorites.map((crypto) => (
                            <li key={crypto.id} className="p-2 border rounded-md my-1">
                                {crypto.cryptocurrency ? (
                                    <>
                                        {crypto.cryptocurrency.name} ({crypto.cryptocurrency.symbol}) -
                                        <span className="text-green-600 font-semibold">
                                        {formatFinancialNumber(crypto.cryptocurrency.latest_data?.price)}
                                        </span><br/>

                                        <span className="text-green-600 font-semibold">
                                        {formatFinancialNumber(crypto.cryptocurrency.latest_data?.market_cap)}
                                        </span><br/>

                                        <span className="text-green-600 font-semibold">
                                        {formatFinancialNumber(crypto.cryptocurrency.latest_data?.percent_change_24h, false, true)}
                                        </span><br/>

                                        <span className="text-green-600 font-semibold">
                                        {formatFinancialNumber(crypto.cryptocurrency.latest_data?.volume)}
                                        </span><br/>
                                    </>
                                ) : (
                                    <span className="text-gray-500">Cargando...</span>
                                )}
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
