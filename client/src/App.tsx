import './App.css'
import TimeReportFilterForm from './components/TimeReportFilterForm'
import TimeReportCreateForm from './components/TimeReportCreateForm'
import TimeReportTable from './components/TimeReportTable'
import useTimeReports from './hooks/useTimeReports';
import useWorkplaces from './hooks/useWorkplaces';
import { useMemo, useState } from 'react';
import type { TimeReportFilters } from './types';

function App() {
  const [filters, setFilters] = useState<TimeReportFilters>({});
  
  const { timeReports, refreshTimeReports, isLoading } = useTimeReports(filters);
  const { workplaces } = useWorkplaces();

  const workplacesMap = useMemo(() => {
    return new Map(workplaces.map(wp => [wp.id, wp]));
  }, [workplaces]);

  return (
    <main className="app">
      <header className="header">
        <h1>Tidrapportering</h1>
      </header>
      <div className="layout">
        <div className="column">
          <section>
            <h2>Filtrera rapporter</h2>
            <TimeReportFilterForm 
              workplaces={workplaces} 
              onFilterChange={setFilters} 
            />
          </section>
          <section>
            <h2>Befintliga rapporter</h2>
            {isLoading ? (
              <p>Laddar...</p>
            ) : (
              <TimeReportTable 
                timeReports={timeReports} 
                workplacesMap={workplacesMap} 
              />
            )}
          </section>
        </div>

        <div className="column">
          <section>
            <h2>Skapa ny rapport</h2>
            <TimeReportCreateForm onReportCreated={refreshTimeReports}/>
          </section>
        </div>
      </div>
    </main>
  );
}

export default App
