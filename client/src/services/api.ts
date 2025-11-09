import type { TimeReport, Workplace } from "../types";

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

export const getTimeReports = (): Promise<TimeReport[]> => {
    return fetchApi<TimeReport[]>('timereport');
}
