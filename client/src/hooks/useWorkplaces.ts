import { useEffect, useState } from "react";
import { getWorkplaces } from "../services/api";
import type { Workplace } from "../types";


function useWorkplaces() {
    const [workplaces, setWorkplaces] = useState<Workplace[]>([]);

    useEffect(() => {
        const fetchWorkplaces = async () => {
            const workplaces = await getWorkplaces();
            setWorkplaces(workplaces);
        };

        fetchWorkplaces();
    }, []);

    return { workplaces };
}

export default useWorkplaces;