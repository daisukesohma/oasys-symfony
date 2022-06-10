import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from "@/graphql/event/event-fragment";

export const ADD_DOCUMENT_TO_EVENT = gql`
    mutation addDocumentToEvent ($eventId: String!, $documentId: String!) {
        addDocumentToEvent (eventId: $eventId, documentId: $documentId) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
