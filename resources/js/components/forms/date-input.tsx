import { type ComponentProps } from 'react';

import { Input } from '@/components/ui/input';

type DateInputProps = Omit<ComponentProps<typeof Input>, 'type'>;

export function DateInput(props: DateInputProps) {
    return <Input {...props} type="datetime-local" />;
}
