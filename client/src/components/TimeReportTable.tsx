import type { TimeReport, Workplace } from "../types";

interface TimeReportTableProps {
  timeReports: TimeReport[];
  workplacesMap: Map<number, Workplace>;
}

function TimeReportTable({ timeReports, workplacesMap }: TimeReportTableProps) {
  return (
    <table>
      <thead>
        <tr>
          <th>Datum</th>
          <th>Arbetsplatsnamn</th>
          <th>Timmar</th>
          <th>Info</th>
          <th>Bild</th>
        </tr>
      </thead>
      <tbody>
        {timeReports.length === 0 ? (
          <tr>
            <td colSpan={5}>Inga tidrapporter funna.</td>
          </tr>
        ) : (
          timeReports.map((report) => {
            const workplaceName = workplacesMap.get(report.workplace_id)?.name ?? 'Okänd arbetsplats';

            return (
              <tr key={report.id}>
              <td>{report.date}</td>
              <td>{workplaceName}</td>
              <td>{report.hours}</td>
              <td>{report.info}</td>
              <td><img src={report.image} alt="Bildbeskrivning" /></td>
            </tr>
            )
          })
        )}
      </tbody>
    </table>
  )
}

export default TimeReportTable