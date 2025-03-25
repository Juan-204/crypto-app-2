import { useState } from "react";
import { Disclosure } from "@headlessui/react";
import CryptoChart from "@/Components/CryptoChart"; // Importamos la gráfica

const CryptoAccordion = ({ crypto }) => {
    const [isOpen, setIsOpen] = useState(false);

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


    return (
        <Disclosure>
            {({ open }) => (
                <>
                    <Disclosure.Button
                        onClick={() => setIsOpen(!isOpen)}
                        className="w-full flex justify-between items-center p-4 bg-gray-200 rounded-md my-2"
                    >
                        <span className="text-lg font-semibold">{crypto.cryptocurrency.name} ({crypto.cryptocurrency.symbol})</span>
                        <span>{open ? "▲" : "▼"}</span>
                    </Disclosure.Button>

                    <Disclosure.Panel className="p-4 bg-white border rounded-md shadow">
                        {/* Datos de Interés */}
                        {crypto.cryptocurrency.latest_data ? (
                            <div className="mb-4">
                                <p><strong>Precio:</strong> {formatFinancialNumber(crypto.cryptocurrency.latest_data.price)}</p>
                                <p><strong>Capitalización:</strong> {formatFinancialNumber(crypto.cryptocurrency.latest_data.market_cap)}</p>
                                <p><strong>Volumen 24h:</strong> {formatFinancialNumber(crypto.cryptocurrency.latest_data.volume)}</p>
                                <p><strong>Cambio 24h:</strong> {formatFinancialNumber(crypto.cryptocurrency.latest_data.percent_change_24h, false, true)}%</p>
                            </div>
                        ) : (
                            <p className="text-gray-500">Cargando datos...</p>
                        )}

                        {/* Gráfica (solo se carga si el acordeón está abierto) */}
                        {isOpen && <CryptoChart cryptocurrencyId={crypto.cryptocurrency.id} />}
                    </Disclosure.Panel>
                </>
            )}
        </Disclosure>
    );
};

export default CryptoAccordion;
