

export interface Workplace {
    id: number;
    name: string;
    created_time: string;
}

export interface TimeReport {
    id: number;
    workplace_id: number;
    date: string;
    hours: number;
    info?: string;
    image?: string;
}