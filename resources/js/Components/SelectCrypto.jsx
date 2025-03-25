import { useState, useEffect } from "react";
import axios from "axios";

const SelectCrypto = ({ auth, onAddFavorite }) => {
    const [search, setSearch] = useState(""); // Estado del input
    const [cryptos, setCryptos] = useState([]);
    const [filteredCryptos, setFilteredCryptos] = useState([]);

    useEffect(() => {
        axios.get("/api/getcrypto") // Llama al backend para obtener las criptos
            .then(response => setCryptos(response.data))
            .catch(error => console.error("Error fetching cryptos:", error));
    }, []);

    const handleSearchChange = (e) => {
        const value = e.target.value;
        setSearch(value);

        if (value.length > 0) {
            setFilteredCryptos(
                cryptos.filter(crypto =>
                    crypto.name.toLowerCase().includes(value.toLowerCase()) ||
                    crypto.symbol.toLowerCase().includes(value.toLowerCase())
                )
            );
        } else {
            setFilteredCryptos([]);
        }
    };

    const handleSelectCrypto = (crypto) => {
        axios.post("/api/favorites", {
            name: crypto.name,
            symbol: crypto.symbol,
            user_id: auth.user.id
        })
        .then(response => {
            onAddFavorite(crypto); // Agregar al estado global
            setSearch(""); // Limpiar el campo del input después de seleccionar
            setFilteredCryptos([]); // Ocultar la lista de sugerencias
        })
        .catch(error => console.error("Error adding favorite:", error));
    };

    return (
        <div className="relative">
            <input
                type="text"
                value={search}
                onChange={handleSearchChange}
                placeholder="Buscar criptomoneda..."
                className="w-full p-2 border rounded-md"
            />
            {filteredCryptos.length > 0 && (
                <ul className="absolute w-full bg-white border rounded-md mt-1 max-h-40 overflow-y-auto shadow-lg">
                    {filteredCryptos.map(crypto => (
                        <li key={crypto.id}
                            onClick={() => handleSelectCrypto(crypto)}
                            className="p-2 cursor-pointer hover:bg-gray-200">
                            {crypto.name} ({crypto.symbol})
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
};

export default SelectCrypto;
