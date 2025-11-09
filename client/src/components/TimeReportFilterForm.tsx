import { useState } from "react";
import type { TimeReportFilters, Workplace } from "../types";
import FormGroup from "./FormGroup/FormGroup";

interface TimeReportFilterFormProps {
  workplaces: Workplace[];
  onFilterChange?: (filters: TimeReportFilters) => void;
}

function TimeReportFilterForm({ workplaces, onFilterChange }: TimeReportFilterFormProps) {
    const [workplace, setWorkplace] = useState<number | null>(null);
    const [fromDate, setFromDate] = useState<string>('');
    const [toDate, setToDate] = useState<string>('');

    const handleWorkplaceChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const value = e.target.value ? Number(e.target.value) : null;
        setWorkplace(value);
        onFilterChange?.({ workplace: value, from_date: fromDate, to_date: toDate });
    }

    const handleFromDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setFromDate(value);
        onFilterChange?.({ workplace: workplace, from_date: value, to_date: toDate });
    }

    const handleToDateChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setToDate(value);
        onFilterChange?.({ workplace: workplace, from_date: fromDate, to_date: value });
    }

    return (
       <form className="form-stack">
        <FormGroup label="Välj arbetsplats:" htmlFor="workplace-select">
          <select 
            id="workplace-select" 
            name="workplace" 
            value={workplace ?? ''} 
            onChange={handleWorkplaceChange}
          >
            <option value="">--Alla--</option>
            {workplaces.map(wp => (
              <option key={wp.id} value={wp.id}>
                {wp.name}
              </option>
            ))}
          </select>
        </FormGroup>
        <FormGroup label="Från:" htmlFor="from-date">
          <input 
            type="date" 
            id="from-date" 
            name="fromDate" 
            value={fromDate} 
            onChange={handleFromDateChange} 
          />
        </FormGroup>
        <FormGroup label="Till:" htmlFor="to-date">
          <input 
            type="date" 
            id="to-date" 
            name="toDate" 
            value={toDate} 
            onChange={handleToDateChange} 
          />
        </FormGroup>
       </form>
    )
}

export default TimeReportFilterForm