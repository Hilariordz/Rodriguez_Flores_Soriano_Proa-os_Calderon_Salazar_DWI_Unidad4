import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ recetas = [] }) {
    const [query, setQuery] = useState('');

    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Buscador de Recetas 🍽️</h2>}
        >
            <Head title="Dashboard" />

            <div className="p-6 max-w-7xl mx-auto">
                {/* 🔍 Formulario de búsqueda */}
                <form method="GET" action="/buscar" className="flex gap-2 mb-6">
                    <input
                        name="query"
                        value={query}
                        onChange={e => setQuery(e.target.value)}
                        placeholder="Ejemplo: pasta, pollo, ensalada..."
                        className="border border-gray-300 rounded p-2 flex-1 focus:ring focus:ring-blue-200"
                    />
                    <button className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Buscar
                    </button>
                </form>

                {/* 🧾 Resultados */}
                {recetas.results ? (
                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        {recetas.results.map((receta) => (
                            <div key={receta.id} className="p-4 bg-white rounded-2xl shadow hover:shadow-lg transition text-center">
                                <img
                                    src={receta.image}
                                    alt={receta.title}
                                    className="w-full h-40 object-cover rounded-xl mb-3"
                                />
                                <h3 className="font-semibold text-gray-800">{receta.title}</h3>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-500">Escribe algo para buscar recetas...</p>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
