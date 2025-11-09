

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
    image_url?: string;
}

export interface CreateTimeReportData {
    workplace_id: number;
    date: string;
    hours: number;
    info?: string;
    image?: File;
}

export interface TimeReportFilters {
  workplace?: number | null;
  from_date?: string | null;
  to_date?: string | null;
}