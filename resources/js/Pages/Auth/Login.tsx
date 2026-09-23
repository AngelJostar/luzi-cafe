import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { FormEventHandler } from "react";

function Info({ icon, text }: { icon: string; text: string }) {
    return (
        <div className="min-h-16 rounded-md border border-white/70 p-2">
            <span className="block text-base text-[#ffc400]">{icon}</span>
            <span>{text}</span>
        </div>
    );
}

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false as boolean,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Acceso administrativo" />
            <main className="min-h-screen bg-[#062947] px-6 py-10 text-[#062947]">
                <div className="mx-auto grid min-h-[78vh] max-w-5xl items-center gap-6 lg:grid-cols-[1fr_420px]">
                    <section className="hidden text-white lg:block">
                        <img
                            src="/images/luzi-logo-neon.png"
                            alt="Luzi"
                            className="mb-10 h-24 w-auto object-contain"
                        />
                        <h1 className="max-w-xl text-5xl font-black leading-none">
                            Panel web de
                            <br />
                            Superadministrador
                        </h1>
                        <div className="mt-6 grid max-w-xl grid-cols-3 gap-3 text-sm font-bold">
                            <Info icon="♢" text="Rutas protegidas" />
                            <Info icon="◷" text="Sesión con inactividad" />
                            <Info icon="⌁" text="Auditoría completa" />
                        </div>
                    </section>
                    <section className="rounded-lg bg-white p-5 shadow-2xl">
                        <p className="text-[10px] font-black text-[#099aa5]">
                            ACCESO ADMINISTRATIVO
                        </p>
                        <h2 className="mt-1 text-xl font-black">
                            Iniciar sesión
                        </h2>
                        {status && (
                            <p className="mt-3 text-xs font-bold text-green-600">
                                {status}
                            </p>
                        )}
                        <form onSubmit={submit} className="mt-4 space-y-3">
                            <label className="grid gap-1 text-[10px] font-black text-[#987e6b]">
                                CORREO
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    autoComplete="username"
                                    autoFocus
                                    onChange={(event) =>
                                        setData("email", event.target.value)
                                    }
                                    className="h-9 rounded-md border-[#dce2e3] text-sm font-bold"
                                />
                            </label>
                            {errors.email && (
                                <p className="text-xs text-red-600">
                                    {errors.email}
                                </p>
                            )}
                            <label className="grid gap-1 text-[10px] font-black text-[#987e6b]">
                                CONTRASEÑA
                                <input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    autoComplete="current-password"
                                    onChange={(event) =>
                                        setData("password", event.target.value)
                                    }
                                    className="h-9 rounded-md border-[#dce2e3] text-sm"
                                />
                            </label>
                            {errors.password && (
                                <p className="text-xs text-red-600">
                                    {errors.password}
                                </p>
                            )}
                            <button
                                disabled={processing}
                                className="mt-1 h-9 w-full rounded-md bg-[#2db74d] text-xs font-black text-white"
                            >
                                ▣　{processing ? "ENTRANDO..." : "ENTRAR"}
                            </button>
                        </form>
                        <div className="mt-4 border-t pt-3">
                            <p className="text-[10px] font-black text-[#987e6b]">
                                RECUPERACIÓN
                            </p>
                            <div className="mt-2 flex gap-2">
                                {canResetPassword && (
                                    <Link
                                        href={route("password.request")}
                                        className="flex-1 rounded-md border bg-[#fff8ec] px-2 py-2 text-center text-[10px] font-black text-[#062947]"
                                    >
                                        RECUPERAR ACCESO
                                    </Link>
                                )}
                            </div>
                        </div>
                        <Link href={route("register")} className="mt-3 block text-center text-xs font-black text-[#099aa5] underline">
                            REGISTRAR NUEVO USUARIO
                        </Link>
                        <p className="mt-3 rounded bg-[#fff8ec] p-2 text-[10px] font-bold text-[#987e6b]">
                            Usuario local inicial: superadmin@luzi.local. Cambia
                            la contraseña desde Configuración al entrar.
                        </p>
                    </section>
                </div>
            </main>
        </GuestLayout>
    );
}
