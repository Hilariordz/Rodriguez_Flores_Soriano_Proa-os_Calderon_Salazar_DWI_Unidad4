import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <>
            <Head title="Iniciar Sesión">
                <link rel="icon" type="image/png" href="/images/BonAppétit-logo.png" />
            </Head>

            <div className="min-h-screen flex">
                {/* Botón de regreso al inicio */}
                <Link
                    href="/"
                    className="fixed top-6 left-6 z-50 text-white hover:text-pink-400 transition-colors flex items-center gap-2"
                >
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span className="text-sm font-medium">Volver al inicio</span>
                </Link>

                {/* Formulario centrado con imagen de fondo */}
                <div className="w-full relative flex items-center justify-center p-8">
                    {/* Imagen de fondo */}
                    <div className="absolute inset-0">
                        <img 
                            src="https://images.pexels.com/photos/262978/pexels-photo-262978.jpeg" 
                            alt="Background" 
                            className="w-full h-full object-cover"
                        />
                        {/* Overlay negro transparente con blur */}
                        <div className="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
                    </div>

                    <div className="relative z-10 w-full max-w-md">
                        <h1 className="text-4xl font-light text-white mb-12">Iniciar Sesión</h1>

                        {status && (
                            <div className="mb-4 text-sm font-medium text-green-400">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label htmlFor="email" className="block text-sm text-gray-400 mb-2">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    className="w-full px-4 py-3 bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-pink-500 transition-colors"
                                    placeholder="tu@email.com"
                                    autoComplete="username"
                                    required
                                />
                                {errors.email && <p className="mt-2 text-sm text-red-400">{errors.email}</p>}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm text-gray-400 mb-2">
                                    Contraseña
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className="w-full px-4 py-3 bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-pink-500 transition-colors"
                                    placeholder="••••••••••••"
                                    autoComplete="current-password"
                                    required
                                />
                                {errors.password && <p className="mt-2 text-sm text-red-400">{errors.password}</p>}
                            </div>

                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-4 h-4 bg-white/10 border-white/20 text-pink-600 focus:ring-pink-500"
                                />
                                <label htmlFor="remember" className="ml-2 text-sm text-gray-400">
                                    Acepto los términos de servicio
                                </label>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="relative overflow-hidden w-full py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-medium transition-all duration-300 group hover:from-yellow-400 hover:to-yellow-500 shadow-lg hover:shadow-yellow-500/25 disabled:opacity-50"
                            >
                                <span className="absolute inset-0 bg-gradient-to-r from-yellow-300 to-yellow-400 transform -translate-x-full group-hover:translate-x-0 transition-transform duration-300 ease-out"></span>
                                <span className="relative z-10">Iniciar Sesión</span>
                            </button>

                            <div className="text-center">
                                <Link
                                    href={route('register')}
                                    className="text-sm text-gray-400 hover:text-white transition-colors"
                                >
                                    ¿No tienes cuenta?
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
