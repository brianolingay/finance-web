import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid, Package, Settings, ShoppingCart, Wallet } from 'lucide-react';

import { useActiveUrl } from '@/hooks/use-active-url';
import { cn } from '@/lib/utils';
import { dashboard as accountDashboard } from '@/routes/accounts';
import { index as expensesIndex } from '@/routes/accounts/expenses';
import { index as productsIndex } from '@/routes/accounts/inventory/products';
import { create as newSale } from '@/routes/accounts/pos/sales';
import { edit as profileEdit } from '@/routes/profile';
import { type NavItem, type SharedData } from '@/types';

export function AccountBottomNav() {
    const { urlIsActive } = useActiveUrl();
    const { account, abilities } = usePage<SharedData>().props;

    if (!account) {
        return null;
    }

    const items: NavItem[] = [
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
        {
            title: 'Settings',
            href: profileEdit(),
            icon: Settings,
        },
    ];

    return (
        <nav className="fixed inset-x-0 bottom-0 z-40 border-t border-border/80 bg-background/95 backdrop-blur md:hidden">
            <div className="mx-auto flex max-w-7xl items-center justify-around px-3 py-2">
                {items.map((item) => {
                    const isActive = urlIsActive(item.href);

                    return (
                        <Link
                            key={item.title}
                            href={item.href}
                            className={cn(
                                'flex flex-col items-center gap-1 rounded-md px-2 py-1 text-xs font-medium transition-colors',
                                isActive
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground',
                            )}
                        >
                            {item.icon && <item.icon className="h-5 w-5" />}
                            <span>{item.title}</span>
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
