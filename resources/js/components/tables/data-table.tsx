import { type ReactNode } from 'react';

import { ColumnHeader } from '@/components/tables/column-header';
import { EmptyState } from '@/components/tables/empty-state';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { cn } from '@/lib/utils';

interface DataTableColumn<T> {
    key: string;
    label: string;
    className?: string;
    headerClassName?: string;
    cellClassName?: string;
    align?: 'left' | 'center' | 'right';
    render?: (item: T) => ReactNode;
}

interface DataTableProps<T> {
    columns: DataTableColumn<T>[];
    data: T[];
    getRowKey: (item: T) => string | number;
    emptyState?: ReactNode;
    className?: string;
}

export function DataTable<T>({
    columns,
    data,
    getRowKey,
    emptyState,
    className,
}: DataTableProps<T>) {
    return (
        <div className={cn('overflow-hidden rounded-xl border border-border', className)}>
            <Table>
                <TableHeader>
                    <TableRow className="bg-muted/40">
                        {columns.map((column) => (
                            <TableHead
                                key={column.key}
                                className={cn(
                                    column.className,
                                    column.headerClassName,
                                    column.align === 'right'
                                        ? 'text-right'
                                        : column.align === 'center'
                                          ? 'text-center'
                                          : 'text-left',
                                )}
                            >
                                <ColumnHeader label={column.label} />
                            </TableHead>
                        ))}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {data.length === 0 ? (
                        <TableRow>
                            <TableCell colSpan={columns.length}>
                                {emptyState ?? (
                                    <EmptyState
                                        title="Nothing yet"
                                        description="Start by adding your first entry."
                                    />
                                )}
                            </TableCell>
                        </TableRow>
                    ) : (
                        data.map((item) => (
                            <TableRow key={getRowKey(item)}>
                                {columns.map((column) => (
                                    <TableCell
                                        key={column.key}
                                        className={cn(
                                            'align-middle',
                                            column.className,
                                            column.cellClassName,
                                            column.align === 'right'
                                                ? 'text-right'
                                                : column.align === 'center'
                                                  ? 'text-center'
                                                  : 'text-left',
                                        )}
                                    >
                                        {column.render ? column.render(item) : null}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))
                    )}
                </TableBody>
            </Table>
        </div>
    );
}
