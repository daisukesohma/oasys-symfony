import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const CREATE_EVENT = gql`
    mutation createEvent (
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $organizerId: String,
        $dateEvent: String,
        $dateEventEnd: String,
        $modelId: String,
        $programId: String,
        $teamsLink: String,
        $meetingPlace: String,
        $meetingRoom: String,
        $evaluationSurvey: String,
        $numberMaxInvites: Int
    ) {
        createEvent (
            name: $name, 
            description: $description, 
            type: $type,
            userIds: $userIds,
            organizerId: $organizerId,
            dateEvent: $dateEvent,
            dateEventEnd: $dateEventEnd,
            modelId: $modelId,
            programId: $programId,
            teamsLink: $teamsLink,
            meetingPlace: $meetingPlace,
            meetingRoom: $meetingRoom,
            evaluationSurvey: $evaluationSurvey,
            numberMaxInvites: $numberMaxInvites
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
