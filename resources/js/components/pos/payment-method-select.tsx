import { SelectField } from '@/components/forms/select-field';

interface PaymentMethodSelectProps {
    value: string;
    onValueChange: (value: string) => void;
    name?: string;
    label?: string;
}

export function PaymentMethodSelect({
    value,
    onValueChange,
    name = 'payment[method]',
    label = 'Payment method',
}: PaymentMethodSelectProps) {
    return (
        <SelectField
            name={name}
            label={label}
            value={value}
            onValueChange={onValueChange}
            placeholder="Select method"
            options={[
                { value: 'cash', label: 'Cash' },
                { value: 'card', label: 'Card' },
                { value: 'transfer', label: 'Bank transfer' },
                { value: 'other', label: 'Other' },
            ]}
        />
    );
}
