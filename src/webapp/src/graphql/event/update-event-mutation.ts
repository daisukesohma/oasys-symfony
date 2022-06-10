import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const UPDATE_EVENT = gql`
    mutation updateEvent (
        $id: String!,
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $organizerId: String,
        $dateEvent: String,
        $dateEventEnd: String,
        $modelId: String,
        $teamsLink: String,
        $meetingPlace: String,
        $meetingRoom: String,
        $evaluationSurvey: String,
        $programId: String,
        $numberMaxInvites: Int
    ) {
        updateEvent (
            id: $id,
            name: $name, 
            description: $description, 
            type: $type,
            userIds: $userIds,
            organizerId: $organizerId,
            dateEvent: $dateEvent,
            dateEventEnd: $dateEventEnd,
            modelId: $modelId,
            teamsLink: $teamsLink,
            meetingPlace: $meetingPlace,
            meetingRoom: $meetingRoom,
            evaluationSurvey: $evaluationSurvey,
            programId: $programId,
            numberMaxInvites: $numberMaxInvites
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
