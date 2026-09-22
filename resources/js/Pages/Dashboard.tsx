import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { ReactNode, useMemo, useState } from 'react';

type Branch = { id: number; name: string };
type Props = { branches: Branch[]; metrics: { branches: number; activeBranches: number; categories: number; products: number; activeProducts: number } };

const WarningIcon = () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="mb-2 h-6 w-6"><path d="m10.3 3.9-8 14a2 2 0 0 0 1.7 3h16a2 2 0 0 0 1.7-3l-8-14a2 2 0 0 0-3.4 0Z" /><path d="M12 9v4M12 17h.01" /></svg>;

function Empty({ text }: { text: string }) {
    return <div className="flex h-28 flex-col items-center justify-center rounded-lg border border-dashed border-[#d8dfe0] text-sm font-bold text-[#987e6b]"><WarningIcon />{text}</div>;
}

function Metric({ label, value, tone, icon }: { label: string; value: string; tone: string; icon: string }) {
    return <div className="flex min-w-28 items-center gap-2 rounded-lg bg-white px-2 py-2 shadow-sm"><span className={`grid h-7 w-7 place-items-center rounded-md text-sm font-black text-white ${tone}`}>{icon}</span><div><p className="whitespace-nowrap text-[9px] font-black text-[#987e6b]">{label}</p><p className="text-base font-black leading-4">{value}</p></div></div>;
}

export default function Dashboard({ branches }: Props) {
    const [filters, setFilters] = useState({ from: '', to: '', branch: '', status: '', payment: '', channel: '', query: '' });
    const statistics = useMemo(() => [
        { label: 'VENTAS DÍA', value: '$0', tone: 'bg-[#25b85a]', icon: '▣' },
        { label: 'VENTAS MES', value: '$0', tone: 'bg-[#079aa6]', icon: '▥' },
        { label: 'PEDIDOS', value: '0', tone: 'bg-[#ff7a00]', icon: '▱' },
        { label: 'TICKET PROM.', value: '$0', tone: 'bg-[#e94250]', icon: '▤' },
    ], []);
    const operational = [
        { label: 'PENDIENTES', color: 'bg-[#129aa2]' }, { label: 'PREPARACIÓN', color: 'bg-[#ff7a00]' }, { label: 'LISTOS', color: 'bg-[#25b85a]' }, { label: 'CANCELADOS', color: 'bg-[#e94250]' },
    ];
    const change = (key: keyof typeof filters, value: string) => setFilters(current => ({ ...current, [key]: value }));
    const exportDashboard = () => {
        const rows = [['Indicador', 'Valor'], ['Ventas día', '$0'], ['Ventas mes', '$0'], ['Pedidos', '0'], ['Ticket promedio', '$0'], [], ['Estado', 'Total'], ...operational.map(item => [item.label, '0'])];
        const csv = rows.map(row => row.map(value => `"${value}"`).join(',')).join('\n');
        const link = document.createElement('a'); link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' })); link.download = 'luzi-dashboard.csv'; link.click(); URL.revokeObjectURL(link.href);
    };

    return <AuthenticatedLayout header={<div><p className="text-xs font-black tracking-wide text-[#099aa5]">LUZI　›　DASHBOARD</p><h1 className="text-2xl font-black">Dashboard</h1></div>}>
        <Head title="Dashboard LUZI" />
        <div className="space-y-4 p-4 lg:p-7">
            <section className="grid gap-3 rounded-xl border border-[#e5dfd4] bg-white p-3 sm:grid-cols-2 xl:grid-cols-7">
                <Filter label="DESDE"><input value={filters.from} onChange={event => change('from', event.target.value)} type="date" className="rounded-lg border-[#dce2e3] text-sm" /></Filter>
                <Filter label="HASTA"><input value={filters.to} onChange={event => change('to', event.target.value)} type="date" className="rounded-lg border-[#dce2e3] text-sm" /></Filter>
                <Filter label="SUCURSAL"><select value={filters.branch} onChange={event => change('branch', event.target.value)} className="rounded-lg border-[#dce2e3] text-sm"><option value="">Todas</option>{branches.map(branch => <option value={String(branch.id)} key={branch.id}>{branch.name}</option>)}</select></Filter>
                <Filter label="ESTADO"><select value={filters.status} onChange={event => change('status', event.target.value)} className="rounded-lg border-[#dce2e3] text-sm"><option value="">Todos</option><option>Pendiente</option><option>Preparación</option><option>Listo</option><option>Cancelado</option></select></Filter>
                <Filter label="PAGO"><select value={filters.payment} onChange={event => change('payment', event.target.value)} className="rounded-lg border-[#dce2e3] text-sm"><option value="">Todos</option><option>Efectivo</option><option>Tarjeta</option><option>Wallet</option></select></Filter>
                <Filter label="CANAL"><select value={filters.channel} onChange={event => change('channel', event.target.value)} className="rounded-lg border-[#dce2e3] text-sm"><option value="">Todos</option><option>App móvil</option><option>Mostrador</option><option>Pickup</option></select></Filter>
                <Filter label="BUSCAR"><input value={filters.query} onChange={event => change('query', event.target.value)} placeholder="folio, cliente" className="rounded-lg border-[#dce2e3] text-sm" /></Filter>
            </section>

            <section className="flex flex-wrap gap-2">{statistics.map(statistic => <Metric key={statistic.label} {...statistic} />)}</section>

            <section className="rounded-xl border border-[#e5dfd4] bg-white p-4">
                <div className="mb-4 flex flex-wrap items-center justify-between gap-3"><h2 className="text-lg font-black">Resumen operativo</h2><button type="button" onClick={exportDashboard} className="rounded-lg bg-[#099aa5] px-4 py-2 text-xs font-black text-white">⇩　EXPORTAR</button></div>
                <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">{operational.map(item => <div key={item.label} className="flex items-center justify-between rounded-lg border border-[#e2e5e5] px-3 py-2.5"><span className="flex items-center gap-2 text-xs font-black"><i className={`h-2.5 w-2.5 rounded-full ${item.color}`} />{item.label}</span><strong>0</strong></div>)}</div>
                <div className="mt-3 grid gap-3 lg:grid-cols-2"><div><p className="mb-2 text-xs font-black text-[#987e6b]">VENTAS POR SUCURSAL</p><Empty text="Sin datos" /></div><div><p className="mb-2 text-xs font-black text-[#987e6b]">VENTAS POR CATEGORÍA</p><Empty text="Sin datos" /></div></div>
            </section>

            <section className="grid gap-4 lg:grid-cols-2"><Panel title="Productos más vendidos" /><Panel title="Productos con menor rotación" /></section>
        </div>
    </AuthenticatedLayout>;
}

function Filter({ label, children }: { label: string; children: ReactNode }) {
    return <label className="grid gap-1 text-[10px] font-black text-[#987e6b]">{label}{children}</label>;
}

function Panel({ title }: { title: string }) {
    return <section className="rounded-xl border border-[#e5dfd4] bg-white p-4"><h2 className="text-lg font-black">{title}</h2><div className="mt-4"><Empty text="Sin registros" /></div></section>;
}
