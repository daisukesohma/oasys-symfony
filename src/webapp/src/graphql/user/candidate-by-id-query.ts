import gql from 'graphql-tag'

export const CANDIDATE_BY_ID = gql`
    query candidateById ($id: String!, $programId: String, $fetchOnlyAttendingEvents: Boolean) {
        candidateById (id: $id, programId: $programId) {
            user {
                id,
                firstName,
                lastName,
                phone,
                email,
                profilePicture {
                    id
                },
                company {
                    id,
                    name
                },
                address,
                userCity,
                userCodePostal
            },
            program {
                id,
                name,
                status,
                dateStart,
                dateEnd,
                documents {
                    ...CandidateDocumentFragment,
                },
                eventsOrderedByDate (fetchOnlyAttending: $fetchOnlyAttendingEvents, userId: $id) {
                    items {
                        ...CandidateEventFragment,
                    },
                },
                todos (userId: $id) {
                    items {
                        id,
                        label,
                        done,
                    },
                    count,
                },
            },
            eventsWithoutProgram {
                items {
                    ...CandidateEventFragment,
                },
            },
            eventsCount,
            completedEventsCount,
            nextEvent {
                ...CandidateEventFragment,
            },
        }
    }
    fragment CandidateDocumentFragment on Document {
        id,
        fileDescriptor {
            id,
            name
        },
        documentsSignersForUser {
            statusSignature
            memberId
        },
        statusSignature,
        name,
        toBeSigned
        visibility,
        elaborationDate,
        type,
    },
    fragment CandidateEventFragment on Event {
        id,
        dateEvent,
        dateEventEnd,
        name,
        status,
        description,
        type,
        memo,
        teamsLink,
        meetingPlace,
        meetingRoom,
        evaluationSurvey,
        documents {
            ...CandidateDocumentFragment,
        },
        program {
            id,
            name,
        },
        users {
            id,
        },
        rating,
        eventsRates {
            items {
                rateNote,
                user {
                    id
                },
            },
        },
    },
`;
