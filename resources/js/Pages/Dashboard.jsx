import { useState, useEffect } from "react";
import SelectCrypto from "@/Components/SelectCrypto";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import axios from "axios";
import CryptoAccordion from "@/Components/CryptoAccordion";

export default function Dashboard({ auth }) {
    const [favorites, setFavorites] = useState([]);
    const [refreshInterval, setRefreshInterval] = useState(null)

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
        setRefreshInterval(interval)

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


        fetchFavorites();

        if (refreshInterval) {
            clearInterval(refreshInterval)
        }
        const newInterval = setInterval(fetchFavorites, 60000)
        setRefreshInterval(newInterval)
    }

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Favoritos</h2>}
        >
            <Head title="Favoritos" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <SelectCrypto auth={auth} onAddFavorite={handleAddFavorite} />

                    <h2 className="text-xl font-semibold mt-6">Favoritos</h2>
                    <div className="mt-2">
                        {favorites.map((crypto) => (
                            <CryptoAccordion
                            key={crypto.id}
                            crypto={crypto}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
