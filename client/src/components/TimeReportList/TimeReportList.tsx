import './TimeReportList.css';
import type { TimeReport, TimeReportFilters, Workplace } from "../../types";
import TimeReportFilterForm from "../TimeReportFilterForm";
import TimeReportTable from "../TimeReportTable";

interface TimeReportListProps {
  timeReports: TimeReport[];
  workplaces: Workplace[];
  workplacesMap: Map<number, Workplace>;
  isLoading: boolean;
  onFilterChange: (filters: TimeReportFilters) => void;
}

function TimeReportList({ 
  timeReports, 
  workplaces, 
  workplacesMap, 
  isLoading, 
  onFilterChange 
}: TimeReportListProps) {
  return (
    <div className="time-report-list">
      <div className="filter-section">
        <TimeReportFilterForm 
          workplaces={workplaces} 
          onFilterChange={onFilterChange} 
        />
      </div>

      {isLoading ? (
        <p>Laddar...</p>
      ) : (
        <TimeReportTable 
          timeReports={timeReports} 
          workplacesMap={workplacesMap} 
        />
      )}
    </div>
  );
}

export default TimeReportList;