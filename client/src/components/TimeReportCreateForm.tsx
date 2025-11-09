import { useState, useId } from 'react';
import useWorkplaces from '../hooks/useWorkplaces';
import FormGroup from './FormGroup/FormGroup';
import { createTimeReport } from '../services/api';;

interface TimeReportCreateFormProps {
    onReportCreated?: () => void;
}

function TimeReportCreateForm({onReportCreated}: TimeReportCreateFormProps) {
    const dateId = useId();
    const hoursId = useId();
    const workplaceId = useId();
    const infoId = useId();
    const imageId = useId();

    const { workplaces } = useWorkplaces();
    const [date, setDate] = useState('');
    const [hours, setHours] = useState('');
    const [workplace, setWorkplace] = useState('');
    const [info, setInfo] = useState('');
    const [image, setImage] = useState<File | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setIsSubmitting(true);

        try {
            await createTimeReport({
                workplace_id: parseInt(workplace),
                date,
                hours: parseFloat(hours),
                info: info || undefined,
                image: image || undefined,
            });

            // Reset form on success
            setDate('');
            setHours('');
            setWorkplace('');
            setInfo('');
            setImage(null);
            
            onReportCreated?.();
            
            alert('Tidrapport skapad!'); // Replace with better UX later
        } catch (err) {
            setError('Kunde inte skapa tidrapport. Försök igen.');
            console.error(err);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
       <form onSubmit={handleSubmit} className="form-stack vertical">
            {error && <div className="form-error">{error}</div>}
            
            <FormGroup label="Datum:" htmlFor={dateId}>
                <input 
                    type="date" 
                    id={dateId} 
                    name="date" 
                    value={date} 
                    onChange={e => setDate(e.target.value)} 
                    required 
                    disabled={isSubmitting}
                />
            </FormGroup>

            <FormGroup label="Timmar:" htmlFor={hoursId}>
                <input 
                    type="number" 
                    id={hoursId} 
                    name="hours" 
                    value={hours} 
                    onChange={e => setHours(e.target.value)} 
                    required 
                    step="0.01" 
                    disabled={isSubmitting}
                />
            </FormGroup>

            <FormGroup label="Arbetsplats:" htmlFor={workplaceId}>
                <select 
                    id={workplaceId} 
                    name="workplace" 
                    value={workplace} 
                    onChange={e => setWorkplace(e.target.value)} 
                    required
                    disabled={isSubmitting}
                >
                    <option value="" disabled>Välj en arbetsplats</option>
                    {workplaces.map(wp => (
                        <option key={wp.id} value={wp.id}>{wp.name}</option>
                    ))}
                </select>
            </FormGroup>

            <FormGroup label="Övrigt:" htmlFor={infoId}>
                <textarea 
                    id={infoId} 
                    name="info" 
                    value={info} 
                    onChange={e => setInfo(e.target.value)}
                    disabled={isSubmitting}
                ></textarea>
            </FormGroup>

            <FormGroup label="Bild:" htmlFor={imageId}>
                <input 
                    type="file" 
                    id={imageId} 
                    name="image"
                    accept="image/jpeg, image/png"
                    onChange={e => setImage(e.target.files ? e.target.files[0] : null)}
                    disabled={isSubmitting}
                />
            </FormGroup>

            <button type="submit" disabled={isSubmitting}>
                {isSubmitting ? 'Skapar...' : 'Skapa'}
            </button>
       </form>
    );
}

export default TimeReportCreateForm;