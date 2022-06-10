import gql from 'graphql-tag'

export const EVENT_FRAGMENT = gql`
    fragment EventFragment on Event {
        id,
        name,
        description,
        status,
        teamsLink,
        type,
        dateEvent,
        dateEventEnd,
        meetingPlace,
        meetingRoom,
        evaluationSurvey,
        program {
            id,
            name,
            description,
            type,
            dateStart,
            dateEnd,
        },
        eventModel {
            id,
            name,
            type,
            description,
        },
        users {
            ...PartialUserFragment,
        },
        organizer {
            ...PartialUserFragment
        },
        createdAt,
        createdBy {
            ...PartialUserFragment
        },
        updatedAt,
        updatedBy {
            ...PartialUserFragment
        },
        numberMaxInvites,
    }
    fragment PartialUserFragment on User {
        id,
        firstName,
        lastName,
        profilePicture {
            id
        },
    }
`;
