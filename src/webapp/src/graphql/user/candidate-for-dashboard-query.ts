import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from "@/graphql/document/document-fragment";

export const CANDIDATE_FOR_DASHBOARD = gql`
    query candidateForDashboard ($id: String!, $fetchOnlyAttendingEvents: Boolean) {
        candidateById (id: $id) {
            user {
                id,
                firstName,
                lastName,
                phone,
                email,
                profilePicture {
                    id,
                },
                company {
                    id,
                    name,
                    salesforceLink
                },
                coach {
                    ...CoachFragment,
                },
                programsByProgramsUsers {
                    id,
                    name,
                    dateStart,
                    dateEnd,
                    coaches {
                        ...CoachFragment,
                    },
                    documents {
                        ...DocumentFragment,
                    },
                    eventsOrderedByDate (fetchOnlyAttending: $fetchOnlyAttendingEvents, userId: $id) {
                        items {
                            ...CandidateEventFragment,
                        },
                    },
                    type,
                },
                address,
                appointmentBooked,
                hasBeenTransferred,
                coachSpeciality {
                    id,
                }
            },
            eventsWithoutProgram {
                items {
                    ...CandidateEventFragment,
                },
            },
            eventsCount,
            completedEventsCount,
            nextEvent {
                ...CandidateEventFragment
            },
        }
    }
    fragment CoachFragment on User {
        id,
        firstName,
        lastName,
        email,
        phone,
        address,
        function,
        profilePicture {
            id,
        },
        cvFile {
            id,
        },
        coachSpeciality {
            id,
        },
        userCity
    }
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
        meetingRoom,
        meetingPlace,
        evaluationSurvey,
        documents {
            ...DocumentFragment,
        },
        isFull,
        isAttending,
        numberMaxInvites,
        organizer {
            ...CoachFragment,
        },
        isRated,
        userRating {
            starsNumber,
            rateNote,
        },
    },
    ${DOCUMENT_FRAGMENT}
`;
