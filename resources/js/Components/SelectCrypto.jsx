import { useState, useEffect } from "react";
import axios from "axios";

const SelectCrypto = ({ auth, onAddFavorite }) => {
    const [query, setQuery] = useState("");
    const [cryptos, setCryptos] = useState([]);
    const [filteredCryptos, setFilteredCryptos] = useState([]);

    useEffect(() => {
        axios.get("/api/getcrypto")
            .then(response => setCryptos(response.data))
            .catch(error => console.error("Error fetching cryptos:", error));
    }, []);

    useEffect(() => {
        if (query.trim() === "") {
            setFilteredCryptos([]);
        } else {
            setFilteredCryptos(
                cryptos.filter(crypto =>
                    crypto.name.toLowerCase().includes(query.toLowerCase()) ||
                    crypto.symbol.toLowerCase().includes(query.toLowerCase())
                )
            );
        }
    }, [query, cryptos]);

    const handleSelect = (crypto) => {
        setQuery(crypto.name);
        setFilteredCryptos([]);

        // Enviar a favoritos
        axios.post("/api/favorites", {
            user_id: auth.user.id,
            crypto_id: crypto.id,
            name: crypto.name,
            symbol: crypto.symbol
        })
        .then(() => {
            console.log("Agregado a favoritos:", crypto);
            onAddFavorite(crypto); // Llama a la función en el Dashboard
        })
        .catch(error => console.error("Error al agregar a favoritos:", error));
    };

    return (
        <div className="relative w-full max-w-md">
            <input
                type="text"
                className="w-full p-2 border rounded-lg shadow-sm focus:ring focus:ring-blue-300"
                placeholder="Buscar criptomoneda..."
                value={query}
                onChange={(e) => setQuery(e.target.value)}
            />
            {filteredCryptos.length > 0 && (
                <ul className="absolute w-full bg-white border rounded-lg shadow-md mt-1 max-h-60 overflow-auto z-10">
                    {filteredCryptos.map((crypto) => (
                        <li
                            key={crypto.id}
                            className="p-2 hover:bg-blue-100 cursor-pointer"
                            onClick={() => handleSelect(crypto)}
                        >
                            {crypto.name} ({crypto.symbol})
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
};

export default SelectCrypto;
