import type { CreateTimeReportData, TimeReport, Workplace } from "../types";

const API_BASE_URL = '/api'

const fetchApi = async <T>(endpoint: string, options?: RequestInit): Promise<T> => {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, options);
    if (!response.ok) {
        throw new Error(`API request failed with status ${response.status}`);
    }
    return response.json();
}

export const getWorkplaces = (): Promise<Workplace[]> => {
    return fetchApi<Workplace[]>('workplace');
};

interface TimeReportQueryParams {
    workplace?: number;
    from_date?: string;
    to_date?: string;
}

export const getTimeReports = (params?: TimeReportQueryParams): Promise<TimeReport[]> => {
    const queryString = new URLSearchParams({
        ...(params?.workplace && { workplace: String(params.workplace) }),
        ...(params?.from_date && { from_date: params.from_date }),
        ...(params?.to_date && { to_date: params.to_date }),
    }).toString();

    return fetchApi<TimeReport[]>(`timereport?${queryString}`);
}

export const createTimeReport = (data: CreateTimeReportData): Promise<TimeReport> => {
    const formData = new FormData();
    formData.append('workplace_id', String(data.workplace_id));
    formData.append('date', data.date);
    formData.append('hours', String(data.hours));
    if (data.info) {
        formData.append('info', data.info);
    }
    if (data.image) {
        formData.append('image', data.image);
    }

    return fetchApi<TimeReport>('timereport', {
        method: 'POST',
        body: formData
    });
};