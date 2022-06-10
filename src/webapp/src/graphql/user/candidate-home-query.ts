import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from "@/graphql/document/document-fragment";

export const CANDIDATE_EVENT_FRAGMENT = gql`
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
        userCity,
        userCodePostal
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
        isAttending,
        numberMaxInvites,
        organizer {
            ...CoachFragment,
        },
        program {
            id
            ...on ProgramPic {
                appointmentTimeLimit
            }
        }
    }
`;

export const CANDIDATE_HOME = gql`
    query candidateHome ($id: String!) {
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
    ${CANDIDATE_EVENT_FRAGMENT}
    ${DOCUMENT_FRAGMENT}
`;