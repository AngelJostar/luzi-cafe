import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode } from 'react';

type IconProps = { className?: string };

function SidebarIcon({ children, className }: PropsWithChildren<IconProps>) {
    return <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className} aria-hidden="true">{children}</svg>;
}

const ChartIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M3 3v18h18" /><path d="M7 16v-5" /><path d="M12 16V8" /><path d="M17 16v-9" /></SidebarIcon>;
const DollarIcon = ({ className }: IconProps) => <SidebarIcon className={className}><circle cx="12" cy="12" r="8" /><path d="M12 7v10" /><path d="M15 9.5c0-1-1.3-1.8-3-1.8s-3 .8-3 1.8 1.3 1.7 3 2 3 .8 3 1.8-1.3 1.8-3 1.8-3-.8-3-1.8" /></SidebarIcon>;
const StoreIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="m3 9 2-5h14l2 5" /><path d="M5 9v10h14V9" /><path d="M9 19v-5h6v5" /><path d="M3 9c0 1.1.9 2 2 2s2-.9 2-2c0 1.1.9 2 2 2s2-.9 2-2c0 1.1.9 2 2 2s2-.9 2-2c0 1.1.9 2 2 2s2-.9 2-2" /></SidebarIcon>;
const CoffeeIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M4 8h12v7a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4Z" /><path d="M16 10h1a3 3 0 0 1 0 6h-1" /><path d="M6 2v3M10 2v3M14 2v3" /></SidebarIcon>;
const ImageIcon = ({ className }: IconProps) => <SidebarIcon className={className}><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" /></SidebarIcon>;
const BoxesIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="m21 16-9 5-9-5V8l9-5 9 5Z" /><path d="m3.3 7.8 8.7 5 8.7-5M12 22V12.8" /><path d="m7.5 5.5 9 5" /></SidebarIcon>;
const CartIcon = ({ className }: IconProps) => <SidebarIcon className={className}><circle cx="9" cy="20" r="1" /><circle cx="19" cy="20" r="1" /><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.4L21 8H6" /></SidebarIcon>;
const UsersIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8" /></SidebarIcon>;
const BellIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></SidebarIcon>;
const FileIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 2v6h6M8 13h8M8 17h8" /></SidebarIcon>;
const SettingsIcon = ({ className }: IconProps) => <SidebarIcon className={className}><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.1 2.1-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5v.2h-3v-.2a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1-2.1-2.1.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H5.3v-3h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 2.1-2.1.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.5v-.2h3v.2a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1 2.1 2.1-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.5 1h.2v3h-.2a1.7 1.7 0 0 0-1.5 1Z" /></SidebarIcon>;
const ShieldIcon = ({ className }: IconProps) => <SidebarIcon className={className}><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" /><path d="m9 12 2 2 4-4" /></SidebarIcon>;
const LockIcon = ({ className }: IconProps) => <SidebarIcon className={className}><rect x="4" y="10" width="16" height="11" rx="2" /><path d="M8 10V7a4 4 0 0 1 8 0v3M12 15v2" /></SidebarIcon>;

type NavigationItem = {
    label: string;
    icon: (props: IconProps) => ReactNode;
    href: string;
    active: boolean;
    permission: string;
};

const roleLabels: Record<string, string> = {
    superadmin: 'Superadministrador',
    'administrador-general': 'Administrador general',
    'gerente-sucursal': 'Gerente de sucursal',
    cajero: 'Cajero',
    'operador-pedidos': 'Operador de pedidos',
    'encargado-inventario': 'Encargado de inventario',
    cliente: 'Cliente',
};

export default function Authenticated({ header, children }: PropsWithChildren<{ header?: ReactNode }>) {
    const { user, permissions, roles, homeUrl } = usePage().props.auth;
    const roleLabel = roles.map((role) => roleLabels[role] ?? role).join(', ') || 'Sin rol asignado';
    const items: NavigationItem[] = [
        { label: 'Dashboard', icon: ChartIcon, href: route('dashboard'), active: route().current('dashboard'), permission: 'branches.view' },
        { label: 'Ventas', icon: DollarIcon, href: route('sales.index'), active: route().current('sales.index'), permission: 'orders.view' },
        { label: 'Sucursales', icon: StoreIcon, href: route('branches.index'), active: route().current('branches.index'), permission: 'branches.view' },
        { label: 'Productos y menú', icon: CoffeeIcon, href: route('catalog.index'), active: route().current('catalog.index'), permission: 'products.manage' },
        { label: 'CMS e imágenes', icon: ImageIcon, href: route('cms.index'), active: route().current('cms.index'), permission: 'settings.manage' },
        { label: 'Almacén general', icon: BoxesIcon, href: route('inventory.index'), active: route().current('inventory.index'), permission: 'inventory.view' },
        { label: 'Pedidos', icon: CartIcon, href: route('orders.index'), active: route().current('orders.index'), permission: 'orders.view' },
        { label: 'Usuarios clientes', icon: UsersIcon, href: route('customers.index'), active: route().current('customers.index'), permission: 'users.manage' },
        { label: 'Notificaciones', icon: BellIcon, href: route('notifications.index'), active: route().current('notifications.index'), permission: 'orders.view' },
        { label: 'Reportes', icon: FileIcon, href: route('reports.index'), active: route().current('reports.index'), permission: 'reports.view' },
        { label: 'Configuración', icon: SettingsIcon, href: route('settings.index'), active: route().current('settings.index'), permission: 'settings.manage' },
        { label: 'Auditoría', icon: ShieldIcon, href: route('audit.index'), active: route().current('audit.index'), permission: 'settings.manage' },
        { label: 'Roles y permisos', icon: LockIcon, href: route('roles.index'), active: route().current('roles.index'), permission: 'users.manage' },
    ];
    const availableModules = items.filter((item) => permissions[item.permission]);
    const hasAdministrativeAccess = availableModules.length > 0;
    const navigationItems = [
        ...availableModules,
        { label: 'Mi perfil', icon: UsersIcon, href: route('profile.edit'), active: route().current('profile.edit') },
/*
};

export default function Authenticated({ header, children }: PropsWithChildren<{ header?: ReactNode }>) {
    const user = usePage().props.auth.user;
    const items: NavigationItem[] = [
        { label: 'Dashboard', icon: ChartIcon, href: route('dashboard'), active: route().current('dashboard') },
        { label: 'Ventas', icon: DollarIcon, href: route('sales.index'), active: route().current('sales.index') },
        { label: 'Sucursales', icon: StoreIcon, href: route('branches.index'), active: route().current('branches.index') },
        { label: 'Productos y menú', icon: CoffeeIcon, href: route('catalog.index'), active: route().current('catalog.index') },
        { label: 'CMS e imágenes', icon: ImageIcon, href: route('cms.index'), active: route().current('cms.index') },
        { label: 'Almacén general', icon: BoxesIcon, href: route('inventory.index'), active: route().current('inventory.index') },
        { label: 'Pedidos', icon: CartIcon, href: route('orders.index'), active: route().current('orders.index') },
        { label: 'Usuarios clientes', icon: UsersIcon, href: route('customers.index'), active: route().current('customers.index') },
        { label: 'Notificaciones', icon: BellIcon, href: route('notifications.index'), active: route().current('notifications.index') },
        { label: 'Reportes', icon: FileIcon, href: route('reports.index'), active: route().current('reports.index') },
        { label: 'Configuración', icon: SettingsIcon, href: route('settings.index'), active: route().current('settings.index') },
        { label: 'Auditoría', icon: ShieldIcon, href: route('audit.index'), active: route().current('audit.index') },
        { label: 'Roles y permisos', icon: LockIcon, href: route('roles.index'), active: route().current('roles.index') },
*/
    ];

    return (
        <div className="min-h-screen bg-[#f5f1e8] text-[#08294a]">
            <aside className="fixed inset-y-0 hidden w-72 flex-col bg-[#062947] p-4 text-white lg:flex">
                <Link href={homeUrl} className="mb-7 flex items-center gap-3 px-3">
                    <span className="rounded-full bg-[#ffc400] px-3 py-1 text-xs font-black text-[#062947]">{hasAdministrativeAccess ? 'PANEL' : 'CUENTA'}</span>
                    <span className="font-black tracking-widest text-[#1bb3bd]">LUZI</span>
                </Link>
                <div className="mb-5 rounded-xl border border-[#244864] p-3">
                    <strong className="block">{roleLabel}</strong>
                    <span className="text-xs text-[#b9d1dd]">{user.name}</span>
                    {hasAdministrativeAccess && <button type="button" className="mt-3 w-full rounded-lg bg-[#ffc400] py-2 text-xs font-black text-[#062947]">◉ VISTA PREVIA</button>}
                </div>
                <nav className="flex-1 space-y-1 overflow-y-auto">
                    {navigationItems.map((item) => {
/*
                <Link href={route('dashboard')} className="mb-7 flex items-center gap-3 px-3">
                    <span className="rounded-full bg-[#ffc400] px-3 py-1 text-xs font-black text-[#062947]">ADMIN</span>
                    <span className="font-black tracking-widest text-[#1bb3bd]">LUZI</span>
                </Link>
                <div className="mb-5 rounded-xl border border-[#244864] p-3">
                    <strong className="block">Super Admin LUZI</strong>
                    <span className="text-xs text-[#b9d1dd]">{user.name}</span>
                    <button type="button" className="mt-3 w-full rounded-lg bg-[#ffc400] py-2 text-xs font-black text-[#062947]">◉ VISTA PREVIA</button>
                </div>
                <nav className="flex-1 space-y-1 overflow-y-auto">
                    {items.map((item) => {
*/
                        const Icon = item.icon;

                        return <Link key={item.label} href={item.href} className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-bold ${item.active ? 'bg-[#ffc400] text-[#062947]' : 'text-white hover:bg-[#123e5d]'}`}>
                            <Icon className="h-5 w-5 shrink-0" />{item.label}
                        </Link>;
                    })}
                </nav>
                <Link
                    href={route('logout')}
                    method="post"
                    as="button"
                    type="button"
                    className="mt-4 w-full shrink-0 rounded-lg border border-[#b9d1dd] py-2 text-sm font-bold hover:bg-[#123e5d]"
                >
                    Cerrar sesión
                </Link>
            </aside>
            <div className="lg:pl-72">
                <header className="flex min-h-20 items-center justify-between border-b border-[#e4ded2] bg-[#faf7f0] px-6 lg:px-8">
                    {header}
                    <div className="flex shrink-0 items-center gap-2">
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                            type="button"
                            className="rounded-lg bg-[#062947] px-4 py-2 text-sm font-bold text-white hover:bg-[#123e5d] lg:hidden"
                        >
                            Cerrar sesión
                        </Link>
                        <button type="button" className="rounded-lg bg-white px-4 py-2 text-sm font-bold shadow-sm">⟳ Actualizar</button>
                    </div>
                </header>
                <main>
                    {!hasAdministrativeAccess && (
                        <div className="mx-6 mt-6 rounded-xl border border-[#e4ded2] bg-white p-4 text-sm lg:mx-8">
                            Tu cuenta no tiene permisos administrativos. Puedes administrar tu perfil. Si necesitas acceso a los módulos, contacta a un administrador.
                        </div>
                    )}
                    {children}
                </main>
{/*
                <Dropdown>
                    <Dropdown.Trigger><button type="button" className="mt-4 w-full rounded-lg border border-[#b9d1dd] py-2 text-sm font-bold">Cerrar sesión</button></Dropdown.Trigger>
                    <Dropdown.Content>
                        <Dropdown.Link href={route('profile.edit')}>Mi perfil</Dropdown.Link>
                        <Dropdown.Link href={route('logout')} method="post" as="button">Confirmar cierre</Dropdown.Link>
                    </Dropdown.Content>
                </Dropdown>
            </aside>
            <div className="lg:pl-72">
                <header className="flex min-h-20 items-center justify-between border-b border-[#e4ded2] bg-[#faf7f0] px-6 lg:px-8">
                    {header}<button type="button" className="rounded-lg bg-white px-4 py-2 text-sm font-bold shadow-sm">⟳ Actualizar</button>
                </header>
                <main>{children}</main>
*/}
            </div>
        </div>
    );
}
