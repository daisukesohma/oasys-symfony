import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT_FROM_DASHBOARD_EVENT = gql`
    mutation createDocumentFromDashboardEvent (
        $name: String!,
        $fileDescriptorId: String,
        $eventId: String!,
        $elaborationDate: String,
        $livrableId: String
    ) {
        createDocumentFromDashboardEvent (
            name: $name, 
            fileDescriptorId: $fileDescriptorId,    
            eventId: $eventId,
            elaborationDate: $elaborationDate,
            livrableId: $livrableId
        ) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
