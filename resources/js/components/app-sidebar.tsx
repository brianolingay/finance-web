import { Link, usePage } from '@inertiajs/react';
import {
    LayoutGrid,
    Package,
    Settings,
    ShoppingCart,
    Users,
    Wallet,
} from 'lucide-react';

import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { dashboard as accountDashboard } from '@/routes/accounts';
import { index as cashiersIndex } from '@/routes/accounts/cashiers';
import { index as expensesIndex } from '@/routes/accounts/expenses';
import { index as productsIndex } from '@/routes/accounts/inventory/products';
import { create as newSale } from '@/routes/accounts/pos/sales';
import { edit as profileEdit } from '@/routes/profile';
import { type NavItem, type SharedData } from '@/types';

import AppLogo from './app-logo';

export function AppSidebar() {
    const { account, abilities } = usePage<SharedData>().props;

    const mainNavItems: NavItem[] = account
        ? [
              {
                  title: 'Dashboard',
                  href: accountDashboard(account),
                  icon: LayoutGrid,
              },
              ...(abilities?.manageFinance
                  ? [
                        {
                            title: 'Finance',
                            href: expensesIndex(account),
                            icon: Wallet,
                        },
                    ]
                  : []),
              ...(abilities?.createSale
                  ? [
                        {
                            title: 'POS',
                            href: newSale(account),
                            icon: ShoppingCart,
                        },
                    ]
                  : []),
              ...(abilities?.manageInventory
                  ? [
                        {
                            title: 'Inventory',
                            href: productsIndex(account),
                            icon: Package,
                        },
                    ]
                  : []),
              ...(abilities?.manageCashiers
                  ? [
                        {
                            title: 'Cashiers',
                            href: cashiersIndex(account),
                            icon: Users,
                        },
                    ]
                  : []),
              {
                  title: 'Settings',
                  href: profileEdit(),
                  icon: Settings,
              },
          ]
        : [
              {
                  title: 'Dashboard',
                  href: dashboard(),
                  icon: LayoutGrid,
              },
          ];

    const footerNavItems: NavItem[] = [];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link
                                href={
                                    account
                                        ? accountDashboard(account)
                                        : dashboard()
                                }
                                prefetch
                            >
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                {footerNavItems.length > 0 && (
                    <NavFooter items={footerNavItems} className="mt-auto" />
                )}
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
