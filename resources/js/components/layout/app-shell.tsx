import { type ComponentProps } from 'react';

import { AppShell as BaseAppShell } from '@/components/app-shell';

type AppShellProps = ComponentProps<typeof BaseAppShell>;

export function AppShell(props: AppShellProps) {
    return <BaseAppShell {...props} />;
}
