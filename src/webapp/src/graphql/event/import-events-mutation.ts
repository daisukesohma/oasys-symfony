import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const IMPORT_EVENTS = gql`
    mutation importEvents ($programId: String!, $file: Upload!) {
        importEvents (programId: $programId, file: $file) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
