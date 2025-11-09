import { useEffect, useState } from "react";
import { getTimeReports } from "../services/api";
import type { TimeReport, TimeReportFilters } from "../types";

function useTimeReports(filters?: TimeReportFilters) {
    const [timeReports, setTimeReports] = useState<TimeReport[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchTimeReports = async () => {
        try {
            setIsLoading(true);
            setError(null);
            
            const params = filters ? {
                workplaceId: filters.workplaceId ?? undefined,
                fromDate: filters.fromDate ?? undefined,
                toDate: filters.toDate ?? undefined,
            } : undefined;
            
            const reports = await getTimeReports(params);
            setTimeReports(reports);
        } catch (err) {
            setError('Failed to load time reports');
            console.error(err);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchTimeReports();
    }, [filters?.workplaceId, filters?.fromDate, filters?.toDate]);

    return { 
        timeReports, 
        isLoading,
        error,
        refreshTimeReports: fetchTimeReports 
    };
}

export default useTimeReports;