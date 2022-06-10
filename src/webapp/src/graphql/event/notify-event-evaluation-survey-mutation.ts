import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const NOTIFY_EVENT_EVALUATION_SURVEY = gql`
    mutation notifyEventEvaluationSurvey ($eventId: String!, $userId: String!) {
        notifyEventEvaluationSurvey (eventId: $eventId, userId: $userId) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
